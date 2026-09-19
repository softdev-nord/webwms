<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\Shipment;
use WebWMS\Inventory\Domain\ShipmentDispatch;
use WebWMS\Inventory\Domain\ShipmentLabel;

final class ShipmentTest extends TestCase
{
    public function testItNormalizesShipmentMasterData(): void
    {
        $shipment = new Shipment(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf450'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf440'),
            ' ship-1 ',
            ' dhl ',
            ' parcel ',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );

        self::assertSame('SHIP-1', $shipment->shipmentNumber());
        self::assertSame('DHL', $shipment->carrier());
        self::assertSame('PARCEL', $shipment->service());
    }

    public function testItRejectsAnEmptyLabelReference(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ShipmentLabel(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf450'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'TRACK-1',
            ' ',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }

    public function testItNormalizesLabelAndHandoverReferences(): void
    {
        $label = new ShipmentLabel(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf450'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            ' TRACK-1 ',
            ' label://shipment/1 ',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
        $dispatch = new ShipmentDispatch(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf450'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            ' HANDOVER-1 ',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );

        self::assertSame('TRACK-1', $label->trackingNumber());
        self::assertSame('label://shipment/1', $label->labelReference());
        self::assertSame('HANDOVER-1', $dispatch->handoverReference());
    }
}
