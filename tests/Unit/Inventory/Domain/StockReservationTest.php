<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InvalidSerialStockException;
use WebWMS\Inventory\Domain\StockAllocation;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\StockReservation;

final class StockReservationTest extends TestCase
{
    public function testItNormalizesTheOrderReference(): void
    {
        $reservation = new StockReservation(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf420'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            ' SO-2026-1001 ',
            5,
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );

        self::assertSame('SO-2026-1001', $reservation->orderReference());
    }

    public function testItRejectsMultipleUnitsForASerialAllocation(): void
    {
        $this->expectException(InvalidSerialStockException::class);

        new StockAllocation(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf421'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf420'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf403'),
            new StockDimensions(serialNumber: 'SERIAL-1'),
            2,
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }
}
