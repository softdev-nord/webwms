<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use WebWMS\Administration\Domain\Tenant\TenantId;

interface UserAccountRepository
{
    public function existsByEmail(TenantId $tenantId, string $email): bool;

    public function save(UserAccount $user): void;
}
