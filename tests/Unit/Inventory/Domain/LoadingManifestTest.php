<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\LoadingManifest;

final class LoadingManifestTest extends TestCase
{
    public function testItRejectsDuplicateShipments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $shipmentId = new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf451');
        new LoadingManifest(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf460'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            'MAN-1',
            'TOUR-1',
            'TRUCK-1',
            [$shipmentId, $shipmentId],
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }
}
