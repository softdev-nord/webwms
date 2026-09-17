<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Tenant;

interface TenantRepository
{
    public function exists(TenantId $id): bool;

    public function save(Tenant $tenant): void;
}
