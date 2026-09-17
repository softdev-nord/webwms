<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Domain\Access;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\PermissionKey;
use WebWMS\Administration\Domain\Access\Role;
use WebWMS\Administration\Domain\Access\RoleCreated;
use WebWMS\Administration\Domain\Access\RoleId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final class RoleTest extends TestCase
{
    public function testItNormalizesPermissionsAndPreventsDuplicates(): void
    {
        $now = new DateTimeImmutable('2026-09-17T10:00:00+00:00');
        $role = Role::create(
            new RoleId('018f6b7f-75d2-7c4e-8c33-31f91b1cf301'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'role_warehouse_admin',
            'Warehouse administrator',
            [new PermissionKey('inventory.stock.read'), new PermissionKey('inventory.stock.read')],
            $now,
        );

        self::assertSame('ROLE_WAREHOUSE_ADMIN', $role->code());
        self::assertCount(1, $role->permissions());
        self::assertInstanceOf(RoleCreated::class, $role->releaseEvents()[0]);
    }

    public function testItGrantsAndRevokesPermissions(): void
    {
        $role = Role::create(
            new RoleId('018f6b7f-75d2-7c4e-8c33-31f91b1cf301'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'ROLE_PICKER',
            'Picker',
            [],
            new DateTimeImmutable(),
        );
        $permission = new PermissionKey('fulfillment.pick.execute');

        $role->grant($permission, new DateTimeImmutable('+1 minute'));
        self::assertSame([$permission], $role->permissions());

        $role->revoke($permission, new DateTimeImmutable('+2 minutes'));
        self::assertSame([], $role->permissions());
    }
}
