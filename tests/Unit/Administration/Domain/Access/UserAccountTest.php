<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Domain\Access;

use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\RoleId;
use WebWMS\Administration\Domain\Access\UserAccount;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Access\UserStatus;
use WebWMS\Administration\Domain\Tenant\TenantId;

final class UserAccountTest extends TestCase
{
    public function testItCreatesAnActiveUserWithUniqueRoles(): void
    {
        $roleId = new RoleId('018f6b7f-75d2-7c4e-8c33-31f91b1cf301');
        $user = UserAccount::create(
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'ADMIN@EXAMPLE.COM',
            'Warehouse Admin',
            '$2y$13$test-hash',
            [$roleId, $roleId],
            new DateTimeImmutable(),
        );

        self::assertSame('admin@example.com', $user->email());
        self::assertSame(UserStatus::Active, $user->status());
        self::assertSame([$roleId], $user->roles());
    }

    public function testRolesOfAnInactiveUserCannotBeChanged(): void
    {
        $user = UserAccount::create(
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'admin@example.com',
            'Warehouse Admin',
            '$2y$13$test-hash',
            [],
            new DateTimeImmutable(),
        );
        $user->deactivate(new DateTimeImmutable('+1 minute'));

        $this->expectException(DomainException::class);
        $user->assignRole(
            new RoleId('018f6b7f-75d2-7c4e-8c33-31f91b1cf301'),
            new DateTimeImmutable('+2 minutes'),
        );
    }
}
