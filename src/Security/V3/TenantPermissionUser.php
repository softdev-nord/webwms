<?php

declare(strict_types=1);

namespace WebWMS\Security\V3;

use Symfony\Component\Security\Core\User\UserInterface;

interface TenantPermissionUser extends UserInterface
{
    public function tenantId(): string;

    public function actorId(): string;

    public function hasPermission(string $permission): bool;
}
