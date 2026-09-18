<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Api;

use WebWMS\Security\V3\TenantPermissionUser;

final readonly class ApiClientUser implements TenantPermissionUser
{
    /** @param list<string> $permissions */
    public function __construct(
        private string $clientId,
        private string $tenant,
        private array $permissions
    ) {
    }

    public function tenantId(): string
    {
        return $this->tenant;
    }

    public function getUserIdentifier(): string
    {
        return $this->clientId;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return ['ROLE_API_CLIENT'];
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function eraseCredentials(): void
    {
    }
}
