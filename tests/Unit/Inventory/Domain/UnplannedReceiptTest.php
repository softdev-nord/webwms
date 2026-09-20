<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\UnplannedReceipt;
use WebWMS\Inventory\Domain\UnplannedReceiptItem;

final class UnplannedReceiptTest extends TestCase
{
    public function testItRequiresAtLeastOneItem(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new UnplannedReceipt(
            new InventoryId('11111111-1111-4111-8111-111111111111'),
            new TenantId('22222222-2222-4222-8222-222222222222'),
            'GR-1',
            'SUP-1',
            'Supplier',
            null,
            [],
            new UserId('33333333-3333-4333-8333-333333333333'),
            new DateTimeImmutable(),
        );
    }

    public function testSerialReceiptRequiresQuantityOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new UnplannedReceiptItem(
            new InventoryId('11111111-1111-4111-8111-111111111111'),
            new InventoryId('22222222-2222-4222-8222-222222222222'),
            new InventoryId('33333333-3333-4333-8333-333333333333'),
            2,
            StockDimensions::fromInput('available', null, 'SERIAL-1'),
        );
    }
}
