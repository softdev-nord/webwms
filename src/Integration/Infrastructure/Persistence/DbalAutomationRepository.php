<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use WebWMS\Integration\Domain\AutomationDevice;
use WebWMS\Integration\Domain\AutomationDeviceNotFoundException;
use WebWMS\Integration\Domain\AutomationRepository;
use WebWMS\Integration\Domain\DeviceCommand;

final readonly class DbalAutomationRepository implements AutomationRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function addDevice(AutomationDevice $device): void
    {
        $this->assertActor($device->tenantId, $device->createdBy);
        $this->connection->insert('wms_automation_device', [
            'id' => $device->id,
            'tenant_id' => $device->tenantId,
            'code' => $device->code,
            'name' => $device->name,
            'device_type' => $device->type,
            'endpoint_url' => $device->endpointUrl,
            'credential_env' => $device->credentialEnv,
            'active' => $device->active ? 1 : 0,
            'created_by' => $device->createdBy,
            'created_at' => $this->date($device->createdAt),
            'changed_by' => null,
            'changed_at' => null,
        ]);
    }

    public function device(string $tenantId, string $deviceId, bool $activeOnly = false): AutomationDevice
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, code, name, device_type, endpoint_url, credential_env, active, created_by, created_at '
            . 'FROM wms_automation_device WHERE tenant_id = :tenantId AND id = :id'
            . ($activeOnly ? ' AND active = 1' : ''),
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );
        if ($row === false) {
            throw new AutomationDeviceNotFoundException('The automation device does not exist or is inactive in the tenant.');
        }

        return $this->hydrateDevice($row);
    }

    public function changeDeviceStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->update('wms_automation_device', [
            'active' => $active ? 1 : 0,
            'changed_by' => $actorId,
            'changed_at' => $this->date($at),
        ], ['tenant_id' => $tenantId, 'id' => $deviceId]) !== 1) {
            throw new AutomationDeviceNotFoundException('The automation device does not exist in the tenant.');
        }
    }

    public function addCommand(DeviceCommand $command): DeviceCommand
    {
        return $this->connection->transactional(function (Connection $connection) use ($command): DeviceCommand {
            $this->assertActor($command->tenantId, $command->createdBy);
            $valid = $connection->fetchOne(
                'SELECT 1 FROM wms_automation_device d INNER JOIN wms_storage_location l '
                . 'ON l.tenant_id = d.tenant_id WHERE d.id = :deviceId AND d.tenant_id = :tenantId '
                . 'AND d.active = 1 AND l.id = :locationId',
                ['deviceId' => $command->deviceId, 'tenantId' => $command->tenantId, 'locationId' => $command->locationId],
            );
            if ($valid === false) {
                throw new AutomationDeviceNotFoundException('An active automation device and location must exist in the tenant.');
            }

            try {
                $connection->insert('wms_device_command', [
                    'id' => $command->id,
                    'tenant_id' => $command->tenantId,
                    'device_id' => $command->deviceId,
                    'command_type' => $command->commandType,
                    'location_id' => $command->locationId,
                    'reference_type' => $command->referenceType,
                    'reference_id' => $command->referenceId,
                    'request_id' => $command->requestId,
                    'status' => $command->status,
                    'message' => $command->message,
                    'created_by' => $command->createdBy,
                    'created_at' => $this->date($command->createdAt),
                    'changed_by' => null,
                    'changed_at' => null,
                ]);
            } catch (UniqueConstraintViolationException) {
                $existing = $this->commandByRequestId($command->tenantId, $command->requestId);
                if ($existing === null) {
                    throw new \LogicException('The idempotent device command could not be resolved.');
                }

                return $existing;
            }

            return $command;
        });
    }

    public function commandByRequestId(string $tenantId, string $requestId): ?DeviceCommand
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM wms_device_command WHERE tenant_id = :tenantId AND request_id = :requestId',
            ['tenantId' => $tenantId, 'requestId' => $requestId],
        );

        return $row === false ? null : $this->hydrateCommand($row);
    }

    public function transitionCommand(string $tenantId, string $commandId, string $status, ?string $message, string $actorId, DateTimeImmutable $at): DeviceCommand
    {
        return $this->connection->transactional(function (Connection $connection) use ($tenantId, $commandId, $status, $message, $actorId, $at): DeviceCommand {
            $this->assertActor($tenantId, $actorId);
            $row = $connection->fetchAssociative(
                'SELECT * FROM wms_device_command WHERE id = :id AND tenant_id = :tenantId FOR UPDATE',
                ['id' => $commandId, 'tenantId' => $tenantId],
            );
            if ($row === false) {
                throw new AutomationDeviceNotFoundException('The device command does not exist in the tenant.');
            }
            $current = (string) $row['status'];
            $allowed = [
                DeviceCommand::STATUS_QUEUED => [DeviceCommand::STATUS_DISPATCHED, DeviceCommand::STATUS_FAILED],
                DeviceCommand::STATUS_DISPATCHED => [DeviceCommand::STATUS_COMPLETED, DeviceCommand::STATUS_FAILED],
                DeviceCommand::STATUS_COMPLETED => [],
                DeviceCommand::STATUS_FAILED => [],
            ];
            if (!in_array($status, $allowed[$current] ?? [], true)) {
                throw new \DomainException(sprintf('The command cannot transition from %s to %s.', $current, $status));
            }
            $connection->update('wms_device_command', [
                'status' => $status,
                'message' => $message,
                'changed_by' => $actorId,
                'changed_at' => $this->date($at),
            ], ['id' => $commandId]);
            $row['status'] = $status;
            $row['message'] = $message;
            $row['changed_by'] = $actorId;
            $row['changed_at'] = $this->date($at);

            return $this->hydrateCommand($row);
        });
    }

    /** @param array<string, mixed> $row */
    private function hydrateDevice(array $row): AutomationDevice
    {
        return new AutomationDevice(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['code'],
            (string) $row['name'],
            (string) $row['device_type'],
            (string) $row['endpoint_url'],
            (string) $row['credential_env'],
            (bool) $row['active'],
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
        );
    }

    /** @param array<string, mixed> $row */
    private function hydrateCommand(array $row): DeviceCommand
    {
        return new DeviceCommand(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['device_id'],
            (string) $row['command_type'],
            (string) $row['location_id'],
            (string) $row['reference_type'],
            (string) $row['reference_id'],
            (string) $row['request_id'],
            (string) $row['status'],
            is_string($row['message']) ? $row['message'] : null,
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
            is_string($row['changed_by']) ? $row['changed_by'] : null,
            is_string($row['changed_at']) ? new DateTimeImmutable($row['changed_at']) : null,
        );
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId',
            ['id' => $actorId, 'tenantId' => $tenantId],
        ) === false) {
            throw new AutomationDeviceNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
