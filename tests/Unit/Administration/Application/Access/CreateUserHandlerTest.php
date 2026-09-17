<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Application\Access;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Application\Access\CreateUser\CreateUserCommand;
use WebWMS\Administration\Application\Access\CreateUser\CreateUserHandler;
use WebWMS\Administration\Application\Access\CreateUser\InvalidRoleAssignmentException;
use WebWMS\Administration\Domain\Access\Role;
use WebWMS\Administration\Domain\Access\RoleId;
use WebWMS\Administration\Domain\Access\RoleRepository;
use WebWMS\Administration\Domain\Access\UserAccount;
use WebWMS\Administration\Domain\Access\UserAccountRepository;
use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final class CreateUserHandlerTest extends TestCase
{
    public function testItPersistsAUserWithTenantRoles(): void
    {
        $users = new UserMemoryRepository();
        $handler = new CreateUserHandler(
            new UserTenantRepository(),
            new AssignableRoleRepository(),
            $users,
        );

        $user = $handler($this->command());

        self::assertSame($user, $users->user);
    }

    public function testItRejectsARoleFromAnotherTenant(): void
    {
        $handler = new CreateUserHandler(
            new UserTenantRepository(),
            new AssignableRoleRepository(false),
            new UserMemoryRepository(),
        );

        $this->expectException(InvalidRoleAssignmentException::class);
        $handler($this->command());
    }

    private function command(): CreateUserCommand
    {
        return new CreateUserCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'admin@example.com',
            'Warehouse Admin',
            '$2y$13$test-hash',
            ['018f6b7f-75d2-7c4e-8c33-31f91b1cf301'],
            new DateTimeImmutable(),
        );
    }
}

final class UserTenantRepository implements TenantRepository
{
    public function exists(TenantId $id): bool
    {
        return true;
    }

    public function save(Tenant $tenant): void
    {
    }
}

final class AssignableRoleRepository implements RoleRepository
{
    public function __construct(private readonly bool $assignable = true)
    {
    }

    public function existsByCode(TenantId $tenantId, string $code): bool
    {
        return false;
    }

    public function allExistForTenant(TenantId $tenantId, array $roleIds): bool
    {
        return $this->assignable;
    }

    public function save(Role $role): void
    {
    }
}

final class UserMemoryRepository implements UserAccountRepository
{
    public ?UserAccount $user = null;

    public function existsByEmail(TenantId $tenantId, string $email): bool
    {
        return false;
    }

    public function save(UserAccount $user): void
    {
        $this->user = $user;
    }
}
