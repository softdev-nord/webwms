<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use WebWMS\Integration\Domain\MachineCommand;
use WebWMS\Integration\Domain\MachineStatus;
use WebWMS\Integration\Domain\WcsConnection;
use WebWMS\Integration\Domain\WcsConnectionNotFoundException;
use WebWMS\Integration\Domain\WcsRepository;

final readonly class DbalWcsRepository implements WcsRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function addConnection(WcsConnection $connection): void
    {
        $this->assertActor($connection->tenantId, $connection->createdBy);
        $this->connection->insert('wms_wcs_connection', [
            'id' => $connection->id, 'tenant_id' => $connection->tenantId, 'code' => $connection->code,
            'name' => $connection->name, 'system_type' => $connection->systemType, 'endpoint_url' => $connection->endpointUrl,
            'credential_env' => $connection->credentialEnv, 'active' => $connection->active ? 1 : 0,
            'created_by' => $connection->createdBy, 'created_at' => $this->date($connection->createdAt),
            'changed_by' => null, 'changed_at' => null,
        ]);
    }

    public function connection(string $tenantId, string $connectionId, bool $activeOnly = false): WcsConnection
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM wms_wcs_connection WHERE tenant_id = :tenantId AND id = :id' . ($activeOnly ? ' AND active = 1' : ''),
            ['tenantId' => $tenantId, 'id' => $connectionId],
        );
        if ($row === false) {
            throw new WcsConnectionNotFoundException('The WCS connection does not exist or is inactive in the tenant.');
        }

        return $this->hydrateConnection($row);
    }

    public function changeConnectionStatus(string $tenantId, string $connectionId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->update('wms_wcs_connection', [
            'active' => $active ? 1 : 0, 'changed_by' => $actorId, 'changed_at' => $this->date($at),
        ], ['tenant_id' => $tenantId, 'id' => $connectionId]) !== 1) {
            throw new WcsConnectionNotFoundException('The WCS connection does not exist in the tenant.');
        }
    }

    public function addCommand(MachineCommand $command): MachineCommand
    {
        return $this->connection->transactional(function (Connection $connection) use ($command): MachineCommand {
            $this->assertActor($command->tenantId, $command->createdBy);
            $this->connection($command->tenantId, $command->connectionId, true);

            try {
                $connection->insert('wms_machine_command', [
                    'id' => $command->id, 'tenant_id' => $command->tenantId, 'connection_id' => $command->connectionId,
                    'command_type' => $command->commandType, 'source' => $command->source, 'destination' => $command->destination,
                    'load_unit' => $command->loadUnit, 'request_id' => $command->requestId, 'status' => $command->status,
                    'message' => $command->message, 'created_by' => $command->createdBy, 'created_at' => $this->date($command->createdAt),
                    'changed_by' => null, 'changed_at' => null,
                ]);
            } catch (UniqueConstraintViolationException) {
                $existing = $this->commandByRequestId($command->tenantId, $command->requestId);
                if ($existing === null) {
                    throw new \LogicException('The idempotent machine command could not be resolved.');
                }

                return $existing;
            }

            return $command;
        });
    }

    public function commandByRequestId(string $tenantId, string $requestId): ?MachineCommand
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM wms_machine_command WHERE tenant_id = :tenantId AND request_id = :requestId', ['tenantId' => $tenantId, 'requestId' => $requestId]);

        return $row === false ? null : $this->hydrateCommand($row);
    }

    public function transitionCommand(string $tenantId, string $commandId, string $status, ?string $message, string $actorId, DateTimeImmutable $at): MachineCommand
    {
        return $this->connection->transactional(function (Connection $connection) use ($tenantId, $commandId, $status, $message, $actorId, $at): MachineCommand {
            $this->assertActor($tenantId, $actorId);
            $row = $connection->fetchAssociative('SELECT * FROM wms_machine_command WHERE tenant_id = :tenantId AND id = :id FOR UPDATE', ['tenantId' => $tenantId, 'id' => $commandId]);
            if ($row === false) {
                throw new WcsConnectionNotFoundException('The machine command does not exist in the tenant.');
            }
            $allowed = [
                MachineCommand::STATUS_QUEUED => [MachineCommand::STATUS_DISPATCHED, MachineCommand::STATUS_CANCELLED, MachineCommand::STATUS_FAILED],
                MachineCommand::STATUS_DISPATCHED => [MachineCommand::STATUS_ACCEPTED, MachineCommand::STATUS_CANCELLED, MachineCommand::STATUS_FAILED],
                MachineCommand::STATUS_ACCEPTED => [MachineCommand::STATUS_COMPLETED, MachineCommand::STATUS_CANCELLED, MachineCommand::STATUS_FAILED],
                MachineCommand::STATUS_COMPLETED => [],
                MachineCommand::STATUS_FAILED => [],
                MachineCommand::STATUS_CANCELLED => [],
            ];
            $current = (string) $row['status'];
            if (!in_array($status, $allowed[$current] ?? [], true)) {
                throw new \DomainException(sprintf('The machine command cannot transition from %s to %s.', $current, $status));
            }
            $connection->update('wms_machine_command', ['status' => $status, 'message' => $message, 'changed_by' => $actorId, 'changed_at' => $this->date($at)], ['id' => $commandId]);
            $row['status'] = $status;
            $row['message'] = $message;
            $row['changed_by'] = $actorId;
            $row['changed_at'] = $this->date($at);

            return $this->hydrateCommand($row);
        });
    }

    public function addMachineStatus(MachineStatus $status): MachineStatus
    {
        return $this->connection->transactional(function (Connection $connection) use ($status): MachineStatus {
            $this->assertActor($status->tenantId, $status->recordedBy);
            $this->connection($status->tenantId, $status->connectionId, true);
            if ($status->commandId !== null && $connection->fetchOne('SELECT 1 FROM wms_machine_command WHERE tenant_id = :tenantId AND id = :id', ['tenantId' => $status->tenantId, 'id' => $status->commandId]) === false) {
                throw new WcsConnectionNotFoundException('The referenced machine command does not exist in the tenant.');
            }

            try {
                $connection->insert('wms_machine_status', [
                    'id' => $status->id, 'tenant_id' => $status->tenantId, 'connection_id' => $status->connectionId,
                    'command_id' => $status->commandId, 'machine_code' => $status->machineCode, 'status' => $status->status,
                    'message' => $status->message, 'external_event_id' => $status->externalEventId,
                    'recorded_by' => $status->recordedBy, 'recorded_at' => $this->date($status->recordedAt),
                ]);
            } catch (UniqueConstraintViolationException) {
                $existing = $this->statusByExternalEventId($status->tenantId, $status->externalEventId);
                if ($existing === null) {
                    throw new \LogicException('The idempotent machine status could not be resolved.');
                }

                return $existing;
            }

            return $status;
        });
    }

    public function statusByExternalEventId(string $tenantId, string $externalEventId): ?MachineStatus
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM wms_machine_status WHERE tenant_id = :tenantId AND external_event_id = :eventId', ['tenantId' => $tenantId, 'eventId' => $externalEventId]);

        return $row === false ? null : $this->hydrateStatus($row);
    }

    /** @param array<string, mixed> $row */
    private function hydrateConnection(array $row): WcsConnection
    {
        return new WcsConnection((string) $row['id'], (string) $row['tenant_id'], (string) $row['code'], (string) $row['name'], (string) $row['system_type'], (string) $row['endpoint_url'], (string) $row['credential_env'], (bool) $row['active'], (string) $row['created_by'], new DateTimeImmutable((string) $row['created_at']));
    }

    /** @param array<string, mixed> $row */
    private function hydrateCommand(array $row): MachineCommand
    {
        return new MachineCommand((string) $row['id'], (string) $row['tenant_id'], (string) $row['connection_id'], (string) $row['command_type'], (string) $row['source'], (string) $row['destination'], (string) $row['load_unit'], (string) $row['request_id'], (string) $row['status'], is_string($row['message']) ? $row['message'] : null, (string) $row['created_by'], new DateTimeImmutable((string) $row['created_at']), is_string($row['changed_by']) ? $row['changed_by'] : null, is_string($row['changed_at']) ? new DateTimeImmutable($row['changed_at']) : null);
    }

    /** @param array<string, mixed> $row */
    private function hydrateStatus(array $row): MachineStatus
    {
        return new MachineStatus((string) $row['id'], (string) $row['tenant_id'], (string) $row['connection_id'], is_string($row['command_id']) ? $row['command_id'] : null, (string) $row['machine_code'], (string) $row['status'], is_string($row['message']) ? $row['message'] : null, (string) $row['external_event_id'], (string) $row['recorded_by'], new DateTimeImmutable((string) $row['recorded_at']));
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne('SELECT 1 FROM wms_user_account WHERE tenant_id = :tenantId AND id = :id', ['tenantId' => $tenantId, 'id' => $actorId]) === false) {
            throw new WcsConnectionNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
