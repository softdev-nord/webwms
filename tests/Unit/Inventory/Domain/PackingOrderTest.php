<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\PackingOrder;

final class PackingOrderTest extends TestCase
{
    public function testItNormalizesTheCodeAndRetainsItsSourcePickList(): void
    {
        $order = new PackingOrder(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf440'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf441'),
            ' pack-1 ',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );

        self::assertSame('PACK-1', $order->code());
        self::assertSame('018f6b7f-75d2-7c4e-8c33-31f91b1cf441', $order->pickListId()->value());
    }
}
