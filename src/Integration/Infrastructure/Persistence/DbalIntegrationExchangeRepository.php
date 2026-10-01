<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Integration\Domain\CommerceConnection;
use WebWMS\Integration\Domain\IntegrationExchangeJob;
use WebWMS\Integration\Domain\IntegrationExchangeRepository;
use WebWMS\Integration\Domain\IntegrationMapping;

readonly class DbalIntegrationExchangeRepository implements IntegrationExchangeRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function mappingsFor(string $tenantId, string $systemType, string $messageType): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, tenant_id, system_type, message_type, source_field, target_field, transformation, created_by, created_at '
            . 'FROM wms_integration_mapping WHERE tenant_id = :tenantId AND system_type = :systemType '
            . 'AND message_type = :messageType ORDER BY source_field',
            ['tenantId' => $tenantId, 'systemType' => $systemType, 'messageType' => $messageType],
        );

        return array_map(static fn (array $row): IntegrationMapping => new IntegrationMapping(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['system_type'],
            (string) $row['message_type'],
            (string) $row['source_field'],
            (string) $row['target_field'],
            (string) $row['transformation'],
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
        ), $rows);
    }

    public function addJob(IntegrationExchangeJob $job): void
    {
        $this->connection->transactional(function () use ($job): void {
            $this->assertTenantUser($job->tenantId, $job->createdBy);
            $this->connection->insert('wms_integration_job', [
                'id' => $job->id,
                'tenant_id' => $job->tenantId,
                'direction' => $job->direction,
                'format' => $job->format,
                'resource_type' => $job->resourceType,
                'source_reference' => $job->sourceReference,
                'status' => 'completed',
                'row_count' => count($job->rows),
                'payload' => json_encode($job->rows, JSON_THROW_ON_ERROR),
                'error_message' => null,
                'created_by' => $job->createdBy,
                'created_at' => $job->createdAt->format('Y-m-d H:i:s.u'),
                'completed_at' => $job->createdAt->format('Y-m-d H:i:s.u'),
            ]);
        });
    }

    public function addMapping(IntegrationMapping $mapping): void
    {
        $this->assertTenantUser($mapping->tenantId, $mapping->createdBy);
        $this->connection->insert('wms_integration_mapping', [
            'id' => $mapping->id,
            'tenant_id' => $mapping->tenantId,
            'system_type' => $mapping->systemType,
            'message_type' => $mapping->messageType,
            'source_field' => $mapping->sourceField,
            'target_field' => $mapping->targetField,
            'transformation' => $mapping->transformation,
            'created_by' => $mapping->createdBy,
            'created_at' => $mapping->createdAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function addCommerceConnection(CommerceConnection $connection): void
    {
        $this->assertTenantUser($connection->tenantId, $connection->createdBy);
        $this->connection->insert('wms_commerce_connection', [
            'id' => $connection->id,
            'tenant_id' => $connection->tenantId,
            'name' => $connection->name,
            'channel_type' => $connection->channelType,
            'endpoint_url' => $connection->endpointUrl,
            'credential_env' => $connection->credentialEnv,
            'active' => $connection->active ? 1 : 0,
            'created_by' => $connection->createdBy,
            'created_at' => $connection->createdAt->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function addChannelOrder(
        string $id,
        string $tenantId,
        string $connectionId,
        string $externalOrderId,
        array $payload,
        string $importedBy,
        DateTimeImmutable $importedAt,
    ): string {
        return $this->connection->transactional(function () use ($id, $tenantId, $connectionId, $externalOrderId, $payload, $importedBy, $importedAt): string {
            $this->assertTenantUser($tenantId, $importedBy);
            $connectionExists = $this->connection->fetchOne(
                'SELECT 1 FROM wms_commerce_connection WHERE id = :id AND tenant_id = :tenantId AND active = 1',
                ['id' => $connectionId, 'tenantId' => $tenantId],
            );
            if ($connectionExists === false) {
                throw new \InvalidArgumentException('The active commerce connection does not exist in the tenant.');
            }

            $existingId = $this->connection->fetchOne(
                'SELECT id FROM wms_channel_order WHERE tenant_id = :tenantId AND connection_id = :connectionId '
                . 'AND external_order_id = :externalOrderId',
                ['tenantId' => $tenantId, 'connectionId' => $connectionId, 'externalOrderId' => $externalOrderId],
            );
            if (is_string($existingId)) {
                return $existingId;
            }

            $this->connection->insert('wms_channel_order', [
                'id' => $id,
                'tenant_id' => $tenantId,
                'connection_id' => $connectionId,
                'external_order_id' => $externalOrderId,
                'status' => 'imported',
                'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
                'imported_by' => $importedBy,
                'imported_at' => $importedAt->format('Y-m-d H:i:s.u'),
            ]);

            return $id;
        });
    }

    private function assertTenantUser(string $tenantId, string $userId): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
            ['userId' => $userId, 'tenantId' => $tenantId],
        );
        if ($exists === false) {
            throw new \InvalidArgumentException('The integration actor must exist in the tenant.');
        }
    }
}
