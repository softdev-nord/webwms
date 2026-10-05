<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\V3;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use WebWMS\Security\V3\DbalUserProvider;

class DbalUserProviderTest extends TestCase
{
    public function testItDerivesTheTenantFromTheUniqueEmailAddress(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::stringNotContains('u.tenant_id = :tenantId'),
                ['email' => 'admin@example.com', 'status' => 'active'],
            )
            ->willReturn([[
                'id' => 'user-id',
                'tenant_id' => 'tenant-id',
                'email' => 'admin@example.com',
                'display_name' => 'Administrator',
                'password_hash' => 'hash',
                'role_code' => 'ROLE_ADMIN',
                'permission_key' => 'administration.read',
            ]]);

        $user = (new DbalUserProvider($connection))->loadUserByIdentifier('ADMIN@example.com');

        self::assertSame('tenant-id', $user->tenantId());
        self::assertSame('tenant-id|admin@example.com', $user->getUserIdentifier());
    }

    public function testItRejectsAnEmailAssignedToMultipleTenants(): void
    {
        $connection = $this->createStub(Connection::class);
        $connection->method('fetchAllAssociative')->willReturn([
            ['id' => 'user-a'],
            ['id' => 'user-b'],
        ]);

        $this->expectException(UserNotFoundException::class);

        (new DbalUserProvider($connection))->loadUserByIdentifier('admin@example.com');
    }
}
