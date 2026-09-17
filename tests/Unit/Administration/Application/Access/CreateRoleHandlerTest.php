<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Application\Access;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Application\Access\CreateRole\CreateRoleCommand;
use WebWMS\Administration\Application\Access\CreateRole\CreateRoleHandler;
use WebWMS\Administration\Application\Access\CreateRole\RoleCodeAlreadyExistsException;
use WebWMS\Administration\Domain\Access\Role;
use WebWMS\Administration\Domain\Access\RoleRepository;
use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final class CreateRoleHandlerTest extends TestCase
{
    public function testItPersistsARoleForAnExistingTenant(): void
    {
        $roles = new RoleMemoryRepository();
        $handler = new CreateRoleHandler(new ExistingTenantRepository(), $roles);

        $role = $handler(new CreateRoleCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf301',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'ROLE_PICKER',
            'Picker',
            ['fulfillment.pick.execute'],
            new DateTimeImmutable(),
        ));

        self::assertSame($role, $roles->role);
    }

    public function testItRejectsADuplicateRoleCode(): void
    {
        $handler = new CreateRoleHandler(new ExistingTenantRepository(), new RoleMemoryRepository(true));

        $this->expectException(RoleCodeAlreadyExistsException::class);
        $handler(new CreateRoleCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf301',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'ROLE_PICKER',
            'Picker',
            [],
            new DateTimeImmutable(),
        ));
    }
}

final class ExistingTenantRepository implements TenantRepository
{
    public function exists(TenantId $id): bool
    {
        return true;
    }

    public function save(Tenant $tenant): void
    {
    }
}

final class RoleMemoryRepository implements RoleRepository
{
    public ?Role $role = null;

    public function __construct(private readonly bool $exists = false)
    {
    }

    public function existsByCode(TenantId $tenantId, string $code): bool
    {
        return $this->exists;
    }

    public function allExistForTenant(TenantId $tenantId, array $roleIds): bool
    {
        return true;
    }

    public function save(Role $role): void
    {
        $this->role = $role;
    }
}
