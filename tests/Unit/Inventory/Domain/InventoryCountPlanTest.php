<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryCountEntry;
use WebWMS\Inventory\Domain\InventoryCountPlan;
use WebWMS\Inventory\Domain\InventoryId;

final class InventoryCountPlanTest extends TestCase
{
    public function testItRejectsAnInvalidLocationPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InventoryCountPlan(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf490'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf402'),
            'ANNUAL-2026',
            'lower case',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }

    public function testItRejectsANegativeCountedQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InventoryCountEntry(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf490'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf491'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            -1,
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }
}
