<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InvalidSerialStockException;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\StockPosting;
use WebWMS\Inventory\Domain\StockStatus;

final class StockDimensionsTest extends TestCase
{
    public function testItNormalizesDimensionsAndBuildsAStableKey(): void
    {
        $dimensions = new StockDimensions(
            StockStatus::QualityInspection,
            ' lot-2026-01 ',
            ' serial-42 ',
            new DateTimeImmutable('2027-05-31 15:30:00'),
        );

        self::assertSame('LOT-2026-01', $dimensions->batchNumber());
        self::assertSame('SERIAL-42', $dimensions->serialNumber());
        self::assertSame('2027-05-31', $dimensions->expiresAt()?->format('Y-m-d'));
        self::assertSame($dimensions->key(), StockDimensions::fromInput(
            'quality_inspection',
            'LOT-2026-01',
            'SERIAL-42',
            new DateTimeImmutable('2027-05-31'),
        )->key());
    }

    public function testItRejectsAnUnknownStockStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);

        StockDimensions::fromInput('quarantine');
    }

    public function testItRejectsMultipleUnitsForASerialNumber(): void
    {
        $this->expectException(InvalidSerialStockException::class);

        new StockPosting(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf404'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf403'),
            2,
            'Initial receipt',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
            new StockDimensions(serialNumber: 'SERIAL-42'),
        );
    }
}
