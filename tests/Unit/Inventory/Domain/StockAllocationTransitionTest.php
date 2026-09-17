<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\AllocationTransitionType;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\StockAllocationTransition;

final class StockAllocationTransitionTest extends TestCase
{
    public function testConsumptionRequiresALedgerEntryId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StockAllocationTransition(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf421'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            AllocationTransitionType::Consume,
            null,
            'Picked',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }

    public function testReleaseDoesNotRequireALedgerEntryId(): void
    {
        $transition = new StockAllocationTransition(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf421'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            AllocationTransitionType::Release,
            null,
            'Order cancelled',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );

        self::assertSame('released', $transition->type()->value);
    }
}
