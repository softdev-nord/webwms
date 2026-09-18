<?php

declare(strict_types=1);

namespace WebWMS\Administration\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use WebWMS\Administration\Domain\Site\Site;
use WebWMS\Administration\Domain\Site\SiteCode;
use WebWMS\Administration\Domain\Site\SiteRepository;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class DoctrineSiteRepository implements SiteRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function existsForTenant(TenantId $tenantId, SiteCode $code): bool
    {
        return $this->connection->fetchOne(
            'SELECT 1 FROM wms_site WHERE tenant_id = :tenantId AND code = :code',
            ['tenantId' => $tenantId->value(), 'code' => $code->value()],
        ) !== false;
    }

    public function save(Site $site): void
    {
        $this->connection->transactional(static function (Connection $connection) use ($site): void {
            $connection->insert('wms_site', [
                'id' => $site->id()->value(),
                'tenant_id' => $site->tenantId()->value(),
                'code' => $site->code()->value(),
                'name' => $site->name(),
                'timezone' => $site->timezone(),
                'status' => $site->status()->value,
                'created_at' => $site->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $site->updatedAt()->format('Y-m-d H:i:s.u'),
            ]);
        });
    }
}
