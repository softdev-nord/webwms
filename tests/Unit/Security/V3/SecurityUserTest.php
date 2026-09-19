<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\V3;

use PHPUnit\Framework\TestCase;
use WebWMS\Security\V3\SecurityUser;

final class SecurityUserTest extends TestCase
{
    public function testItExposesTenantScopedIdentifierRolesAndPermissions(): void
    {
        $user = new SecurityUser(
            'user-id',
            'tenant-id',
            'admin@example.com',
            'password-hash',
            ['ROLE_ADMIN', 'ROLE_ADMIN'],
            ['inventory.stock.read'],
            'Demo Administrator',
        );

        self::assertSame('tenant-id|admin@example.com', $user->getUserIdentifier());
        self::assertSame('admin@example.com', $user->email());
        self::assertSame('Demo Administrator', $user->displayName());
        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
        self::assertTrue($user->hasPermission('inventory.stock.read'));
        self::assertFalse($user->hasPermission('inventory.stock.write'));
    }
}
