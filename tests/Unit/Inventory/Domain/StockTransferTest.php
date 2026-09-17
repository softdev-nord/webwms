<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\StockStatus;
use WebWMS\Inventory\Domain\StockTransfer;

final class StockTransferTest extends TestCase
{
    public function testItBuildsCorrelatedTransferPostings(): void
    {
        $transfer = $this->transfer(
            new StockDimensions(StockStatus::QualityInspection, 'LOT-1'),
            new StockDimensions(StockStatus::Available, 'LOT-1'),
        );

        self::assertSame(-5, $transfer->sourcePosting()->quantityDelta());
        self::assertSame(5, $transfer->destinationPosting()->quantityDelta());
        self::assertSame('quality_inspection', $transfer->sourcePosting()->dimensions()->status()->value);
        self::assertSame('available', $transfer->destinationPosting()->dimensions()->status()->value);
    }

    public function testItRejectsAnUnchangedDestination(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $dimensions = new StockDimensions(batchNumber: 'LOT-1');
        $this->transfer($dimensions, $dimensions, sameLocation: true);
    }

    public function testItPreservesIdentityDimensions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->transfer(
            new StockDimensions(batchNumber: 'LOT-1'),
            new StockDimensions(batchNumber: 'LOT-2'),
        );
    }

    private function transfer(
        StockDimensions $source,
        StockDimensions $destination,
        bool $sameLocation = false,
    ): StockTransfer {
        return new StockTransfer(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf410'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf411'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf412'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf403'),
            $source,
            new InventoryId($sameLocation
                ? '018f6b7f-75d2-7c4e-8c33-31f91b1cf403'
                : '018f6b7f-75d2-7c4e-8c33-31f91b1cf405'),
            $destination,
            5,
            'Release after quality inspection',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable('2026-09-17 12:00:00'),
        );
    }
}
