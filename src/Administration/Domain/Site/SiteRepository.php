<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Site;

use WebWMS\Administration\Domain\Tenant\TenantId;

interface SiteRepository
{
    public function existsForTenant(TenantId $tenantId, SiteCode $code): bool;

    public function save(Site $site): void;
}
