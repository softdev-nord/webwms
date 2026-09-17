<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use WebWMS\Administration\Domain\Tenant\TenantId;

interface RoleRepository
{
    public function existsByCode(TenantId $tenantId, string $code): bool;

    /** @param list<RoleId> $roleIds */
    public function allExistForTenant(TenantId $tenantId, array $roleIds): bool;

    public function save(Role $role): void;
}
