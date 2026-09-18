<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\OutboundOrder;
use WebWMS\Inventory\Domain\OutboundOrderItem;

final class OutboundOrderTest extends TestCase
{
    public function testItCreatesAnOrderWithValidatedItems(): void
    {
        $order = $this->order([
            new OutboundOrderItem($this->id('501'), $this->id('601'), 4),
            new OutboundOrderItem($this->id('502'), $this->id('602'), 2),
        ]);

        self::assertSame('ORDER-100', $order->orderNumber());
        self::assertCount(2, $order->items());
    }

    public function testItRejectsDuplicateProducts(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->order([
            new OutboundOrderItem($this->id('501'), $this->id('601'), 4),
            new OutboundOrderItem($this->id('502'), $this->id('601'), 2),
        ]);
    }

    /** @param list<OutboundOrderItem> $items */
    private function order(array $items): OutboundOrder
    {
        return new OutboundOrder(
            $this->id('500'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'ORDER-100',
            'CUSTOMER-1',
            $items,
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable('2026-09-18 19:00:00'),
        );
    }

    private function id(string $suffix): InventoryId
    {
        return new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf' . $suffix);
    }
}
