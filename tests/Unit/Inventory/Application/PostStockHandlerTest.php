<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\PostStockCommand;
use WebWMS\Inventory\Application\PostStockHandler;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\PickConfirmation;
use WebWMS\Inventory\Domain\PickConfirmationResult;
use WebWMS\Inventory\Domain\PickList;
use WebWMS\Inventory\Domain\PickListAssignment;
use WebWMS\Inventory\Domain\StockAllocation;
use WebWMS\Inventory\Domain\StockAllocationResult;
use WebWMS\Inventory\Domain\StockAllocationTransition;
use WebWMS\Inventory\Domain\StockFulfillmentResult;
use WebWMS\Inventory\Domain\StockPosting;
use WebWMS\Inventory\Domain\StockReservation;
use WebWMS\Inventory\Domain\StockTransfer;
use WebWMS\Inventory\Domain\StockTransferResult;
use WebWMS\Inventory\Domain\StorageLocation;
use WebWMS\Inventory\Domain\Warehouse;

final class PostStockHandlerTest extends TestCase
{
    public function testItDelegatesAValidatedPostingAndReturnsTheBalance(): void
    {
        $repository = new InventoryMemoryRepository();
        $handler = new PostStockHandler($repository);

        $balance = $handler(new PostStockCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf404',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf401',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf403',
            10,
            'Initial receipt',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable(),
            'blocked',
            'lot-2026-01',
            null,
            new DateTimeImmutable('2027-05-31'),
        ));

        self::assertSame(10, $balance);
        self::assertSame(10, $repository->posting?->quantityDelta());
        self::assertSame('blocked', $repository->posting?->dimensions()->status()->value);
        self::assertSame('LOT-2026-01', $repository->posting?->dimensions()->batchNumber());
    }
}

final class InventoryMemoryRepository implements InventoryRepository
{
    public ?StockPosting $posting = null;

    public function saveProduct(ProductReference $product): void
    {
    }

    public function saveWarehouse(Warehouse $warehouse): void
    {
    }

    public function saveLocation(StorageLocation $location): void
    {
    }

    public function post(StockPosting $posting): int
    {
        $this->posting = $posting;

        return 10;
    }

    public function transfer(StockTransfer $transfer): StockTransferResult
    {
        return new StockTransferResult(0, 0);
    }

    public function saveReservation(StockReservation $reservation): void
    {
    }

    public function allocate(StockAllocation $allocation): StockAllocationResult
    {
        return new StockAllocationResult(0, 0, 0);
    }

    public function transitionAllocation(StockAllocationTransition $transition): StockFulfillmentResult
    {
        return new StockFulfillmentResult('released', 'open', 0);
    }

    public function savePickList(PickList $pickList): void {}
    public function assignPickList(PickListAssignment $assignment): void {}
    public function confirmPick(PickConfirmation $confirmation): PickConfirmationResult
    {
        return new PickConfirmationResult('picked', 'completed', 'fulfilled');
    }
}
