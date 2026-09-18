<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Infrastructure\Api;

use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Infrastructure\Api\ApiClientUser;

final class ApiClientUserTest extends TestCase
{
    public function testItExposesOnlyItsTenantAndGrantedPermissions(): void
    {
        $user = new ApiClientUser(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf601',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            ['inventory.product.read', 'inventory.stock.read'],
        );

        self::assertSame('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8', $user->tenantId());
        self::assertTrue($user->hasPermission('inventory.stock.read'));
        self::assertFalse($user->hasPermission('inventory.product.write'));
        self::assertSame(['ROLE_API_CLIENT'], $user->getRoles());
    }
}
