<?php

declare(strict_types=1);

namespace WebWMS\Administration\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final readonly class DoctrineTenantRepository implements TenantRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function exists(TenantId $id): bool
    {
        return $this->connection->fetchOne(
            'SELECT 1 FROM wms_tenant WHERE id = :id',
            ['id' => $id->value()],
        ) !== false;
    }

    public function save(Tenant $tenant): void
    {
        $this->connection->transactional(static function (Connection $connection) use ($tenant): void {
            $connection->insert('wms_tenant', [
                'id' => $tenant->id()->value(),
                'name' => $tenant->name(),
                'status' => $tenant->status()->value,
                'created_at' => $tenant->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $tenant->updatedAt()->format('Y-m-d H:i:s.u'),
            ]);
        });
    }
}
