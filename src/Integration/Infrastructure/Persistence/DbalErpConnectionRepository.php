<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Integration\Domain\ErpConnection;
use WebWMS\Integration\Domain\ErpConnectionNotFoundException;
use WebWMS\Integration\Domain\ErpConnectionRepository;

final readonly class DbalErpConnectionRepository implements ErpConnectionRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function add(ErpConnection $connection): void
    {
        $referencesExist = $this->connection->fetchOne(
            'SELECT 1 FROM wms_tenant t, wms_user_account u '
            . 'WHERE t.id = :tenantId AND u.id = :createdBy AND u.tenant_id = t.id',
            ['tenantId' => $connection->tenantId, 'createdBy' => $connection->createdBy],
        );
        if ($referencesExist === false) {
            throw new ErpConnectionNotFoundException('The ERP connection tenant and creating user must exist.');
        }
        $this->connection->insert('wms_erp_connection', [
            'id' => $connection->id,
            'tenant_id' => $connection->tenantId,
            'name' => $connection->name,
            'endpoint_url' => $connection->endpointUrl,
            'credential_env' => $connection->credentialEnv,
            'active' => $connection->active ? 1 : 0,
            'created_by' => $connection->createdBy,
            'created_at' => $connection->createdAt->format('Y-m-d H:i:s.u'),
            'changed_by' => null,
            'changed_at' => null,
        ]);
    }

    public function activeForTenant(string $tenantId): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, tenant_id, name, endpoint_url, credential_env, active, created_by, created_at '
            . 'FROM wms_erp_connection WHERE tenant_id = :tenantId AND active = 1 ORDER BY name, id',
            ['tenantId' => $tenantId],
        );

        return array_map(static fn (array $row): ErpConnection => new ErpConnection(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['name'],
            (string) $row['endpoint_url'],
            (string) $row['credential_env'],
            (bool) $row['active'],
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
        ), $rows);
    }

    public function changeStatus(
        string $connectionId,
        string $tenantId,
        bool $active,
        string $changedBy,
        DateTimeImmutable $changedAt
    ): void {
        $userExists = $this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
            ['userId' => $changedBy, 'tenantId' => $tenantId],
        );
        if ($userExists === false) {
            throw new ErpConnectionNotFoundException('The user changing the ERP connection must exist in the tenant.');
        }
        $affected = $this->connection->update('wms_erp_connection', [
            'active' => $active ? 1 : 0,
            'changed_by' => $changedBy,
            'changed_at' => $changedAt->format('Y-m-d H:i:s.u'),
        ], ['id' => $connectionId, 'tenant_id' => $tenantId]);
        if ($affected !== 1) {
            throw new ErpConnectionNotFoundException('The ERP connection does not exist in the tenant.');
        }
    }
}
