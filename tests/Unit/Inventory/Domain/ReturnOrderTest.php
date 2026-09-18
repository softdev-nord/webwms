<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\ReturnItem;
use WebWMS\Inventory\Domain\ReturnOrder;
use WebWMS\Inventory\Domain\ReturnQualityDecision;

final class ReturnOrderTest extends TestCase
{
    public function testItRejectsDuplicateReturnItems(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $item = new ReturnItem(new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf471'), new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'), 1, 'Wrong item');
        new ReturnOrder(new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf470'), new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'), 'RET-1', 'ORDER-1', [$item, $item], new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'), new DateTimeImmutable());
    }

    public function testQualityDecisionMapsToStockStatus(): void
    {
        self::assertSame('available', ReturnQualityDecision::Restock->stockStatus()->value);
        self::assertSame('blocked', ReturnQualityDecision::Quarantine->stockStatus()->value);
    }
}
