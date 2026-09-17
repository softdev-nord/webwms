<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\TransferStockCommand;
use WebWMS\Inventory\Application\TransferStockHandler;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PackingCompletion;
use WebWMS\Inventory\Domain\PackingOrder;
use WebWMS\Inventory\Domain\PackingPackage;
use WebWMS\Inventory\Domain\PackingResult;
use WebWMS\Inventory\Domain\PickConfirmation;
use WebWMS\Inventory\Domain\PickConfirmationResult;
use WebWMS\Inventory\Domain\PickList;
use WebWMS\Inventory\Domain\PickListAssignment;
use WebWMS\Inventory\Domain\ProductReference;
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

final class TransferStockHandlerTest extends TestCase
{
    public function testItDelegatesAnAtomicStatusTransfer(): void
    {
        $repository = new TransferMemoryInventoryRepository();
        $handler = new TransferStockHandler($repository);

        $result = $handler(new TransferStockCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf410',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf411',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf412',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf401',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf403',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf403',
            5,
            'Release after quality inspection',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable('2026-09-17 12:00:00'),
            'quality_inspection',
            'available',
            'lot-1',
            null,
            new DateTimeImmutable('2027-05-31'),
        ));

        self::assertSame(5, $result->sourceQuantity);
        self::assertSame(12, $result->destinationQuantity);
        self::assertSame('LOT-1', $repository->transfer?->sourcePosting()->dimensions()->batchNumber());
        self::assertSame(
            'available',
            $repository->transfer?->destinationPosting()->dimensions()->status()->value,
        );
    }
}

final class TransferMemoryInventoryRepository implements InventoryRepository
{
    public ?StockTransfer $transfer = null;

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
        return 0;
    }

    public function transfer(StockTransfer $transfer): StockTransferResult
    {
        $this->transfer = $transfer;

        return new StockTransferResult(5, 12);
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

    public function savePickList(PickList $pickList): void
    {
    }

    public function assignPickList(PickListAssignment $assignment): void
    {
    }

    public function confirmPick(PickConfirmation $confirmation): PickConfirmationResult
    {
        return new PickConfirmationResult('picked', 'completed', 'fulfilled');
    }

    public function savePackingOrder(PackingOrder $order): void
    {
    }

    public function savePackingPackage(PackingPackage $package): void
    {
    }

    public function completePackingOrder(PackingCompletion $completion): PackingResult
    {
        return new PackingResult('completed', 1, 1000);
    }
}
