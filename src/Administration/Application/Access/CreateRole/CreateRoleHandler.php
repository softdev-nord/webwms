<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Access\CreateRole;

use WebWMS\Administration\Domain\Access\PermissionKey;
use WebWMS\Administration\Domain\Access\Role;
use WebWMS\Administration\Domain\Access\RoleId;
use WebWMS\Administration\Domain\Access\RoleRepository;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final readonly class CreateRoleHandler
{
    public function __construct(
        private TenantRepository $tenants,
        private RoleRepository $roles,
    ) {
    }

    public function __invoke(CreateRoleCommand $command): Role
    {
        $tenantId = new TenantId($command->tenantId);

        if (!$this->tenants->exists($tenantId)) {
            throw new TenantNotFoundException(sprintf('Tenant "%s" does not exist.', $tenantId->value()));
        }

        $code = strtoupper(trim($command->code));

        if ($this->roles->existsByCode($tenantId, $code)) {
            throw new RoleCodeAlreadyExistsException(sprintf('Role code "%s" already exists.', $code));
        }

        $permissions = array_map(
            static fn (string $permission): PermissionKey => new PermissionKey($permission),
            $command->permissions,
        );
        $role = Role::create(
            new RoleId($command->roleId),
            $tenantId,
            $code,
            $command->name,
            $permissions,
            $command->occurredAt,
        );
        $this->roles->save($role);

        return $role;
    }
}
