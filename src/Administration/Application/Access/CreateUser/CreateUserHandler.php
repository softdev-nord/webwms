<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Access\CreateUser;

use WebWMS\Administration\Domain\Access\RoleId;
use WebWMS\Administration\Domain\Access\RoleRepository;
use WebWMS\Administration\Domain\Access\UserAccount;
use WebWMS\Administration\Domain\Access\UserAccountRepository;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final readonly class CreateUserHandler
{
    public function __construct(
        private TenantRepository $tenants,
        private RoleRepository $roles,
        private UserAccountRepository $users,
    ) {
    }

    public function __invoke(CreateUserCommand $command): UserAccount
    {
        $tenantId = new TenantId($command->tenantId);

        if (!$this->tenants->exists($tenantId)) {
            throw new TenantNotFoundException(sprintf('Tenant "%s" does not exist.', $tenantId->value()));
        }

        $email = strtolower(trim($command->email));

        if ($this->users->existsByEmail($tenantId, $email)) {
            throw new EmailAlreadyExistsException(sprintf('Email "%s" already exists.', $email));
        }

        $roleIds = array_map(
            static fn (string $roleId): RoleId => new RoleId($roleId),
            array_values(array_unique($command->roleIds)),
        );

        if (!$this->roles->allExistForTenant($tenantId, $roleIds)) {
            throw new InvalidRoleAssignmentException('At least one role does not belong to the tenant.');
        }

        $user = UserAccount::create(
            new UserId($command->userId),
            $tenantId,
            $email,
            $command->displayName,
            $command->passwordHash,
            $roleIds,
            $command->occurredAt,
        );
        $this->users->save($user);

        return $user;
    }
}
