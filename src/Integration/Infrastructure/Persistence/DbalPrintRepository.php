<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use WebWMS\Integration\Domain\Printer;
use WebWMS\Integration\Domain\PrinterNotFoundException;
use WebWMS\Integration\Domain\PrintJob;
use WebWMS\Integration\Domain\PrintRepository;

final readonly class DbalPrintRepository implements PrintRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function addPrinter(Printer $printer): void
    {
        $this->assertActor($printer->tenantId, $printer->createdBy);
        $this->connection->insert('wms_printer', [
            'id' => $printer->id, 'tenant_id' => $printer->tenantId, 'name' => $printer->name,
            'endpoint_url' => $printer->endpointUrl, 'credential_env' => $printer->credentialEnv,
            'active' => $printer->active ? 1 : 0, 'created_by' => $printer->createdBy,
            'created_at' => $printer->createdAt->format('Y-m-d H:i:s.u'), 'changed_by' => null, 'changed_at' => null,
        ]);
    }

    public function printer(string $tenantId, string $id, bool $activeOnly = false): Printer
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, name, endpoint_url, credential_env, active, created_by, created_at '
            . 'FROM wms_printer WHERE tenant_id = :tenantId AND id = :id' . ($activeOnly ? ' AND active = 1' : ''),
            ['tenantId' => $tenantId, 'id' => $id],
        );
        if ($row === false) {
            throw new PrinterNotFoundException('The printer does not exist or is inactive in the tenant.');
        }

        return new Printer((string) $row['id'], (string) $row['tenant_id'], (string) $row['name'], (string) $row['endpoint_url'], (string) $row['credential_env'], (bool) $row['active'], (string) $row['created_by'], new DateTimeImmutable((string) $row['created_at']));
    }

    public function changePrinterStatus(string $tenantId, string $id, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->update('wms_printer', [
            'active' => $active ? 1 : 0, 'changed_by' => $actorId, 'changed_at' => $at->format('Y-m-d H:i:s.u'),
        ], ['tenant_id' => $tenantId, 'id' => $id]) !== 1) {
            throw new PrinterNotFoundException('The printer does not exist in the tenant.');
        }
    }

    public function addJob(PrintJob $job): PrintJob
    {
        $this->assertActor($job->tenantId, $job->createdBy);

        try {
            $this->connection->insert('wms_print_job', $this->jobData($job));
        } catch (UniqueConstraintViolationException) {
            $existing = $this->connection->fetchOne(
                'SELECT id FROM wms_print_job WHERE tenant_id = :tenantId AND idempotency_key = :requestId',
                ['tenantId' => $job->tenantId, 'requestId' => $job->idempotencyKey],
            );
            if (!is_string($existing)) {
                throw new \LogicException('The idempotent print job could not be resolved.');
            }

            return $this->job($job->tenantId, $existing);
        }

        return $job;
    }

    public function job(string $tenantId, string $id): PrintJob
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM wms_print_job WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $id],
        );
        if ($row === false) {
            throw new PrinterNotFoundException('The print job does not exist in the tenant.');
        }

        return new PrintJob(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['printer_id'],
            (string) $row['document_type'],
            (string) $row['document_reference'],
            (string) $row['format'],
            (int) $row['copies'],
            (string) $row['idempotency_key'],
            (string) $row['status'],
            (int) $row['attempts'],
            is_string($row['external_reference']) ? $row['external_reference'] : null,
            is_string($row['last_error']) ? $row['last_error'] : null,
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
            is_string($row['completed_at']) ? new DateTimeImmutable($row['completed_at']) : null,
        );
    }

    public function saveJob(PrintJob $job): void
    {
        $this->connection->update('wms_print_job', [
            'status' => $job->status, 'attempts' => $job->attempts, 'external_reference' => $job->externalReference,
            'last_error' => $job->lastError, 'completed_at' => $job->completedAt?->format('Y-m-d H:i:s.u'),
        ], ['tenant_id' => $job->tenantId, 'id' => $job->id]);
    }

    /** @return array<string, int|string|null> */
    private function jobData(PrintJob $job): array
    {
        return [
            'id' => $job->id, 'tenant_id' => $job->tenantId, 'printer_id' => $job->printerId,
            'document_type' => $job->documentType, 'document_reference' => $job->documentReference,
            'format' => $job->format, 'copies' => $job->copies, 'idempotency_key' => $job->idempotencyKey,
            'status' => $job->status, 'attempts' => $job->attempts, 'external_reference' => $job->externalReference,
            'last_error' => $job->lastError, 'created_by' => $job->createdBy,
            'created_at' => $job->createdAt->format('Y-m-d H:i:s.u'), 'completed_at' => $job->completedAt?->format('Y-m-d H:i:s.u'),
        ];
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId', ['id' => $actorId, 'tenantId' => $tenantId]) === false) {
            throw new PrinterNotFoundException('The acting user must exist in the tenant.');
        }
    }
}
