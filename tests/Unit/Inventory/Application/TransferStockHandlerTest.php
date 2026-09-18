<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\TransferStockCommand;
use WebWMS\Inventory\Application\TransferStockHandler;
use WebWMS\Inventory\Domain\InboundDelivery;
use WebWMS\Inventory\Domain\InboundInspection;
use WebWMS\Inventory\Domain\InboundReceipt;
use WebWMS\Inventory\Domain\InboundResult;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\LoadingCompletion;
use WebWMS\Inventory\Domain\LoadingManifest;
use WebWMS\Inventory\Domain\LoadingResult;
use WebWMS\Inventory\Domain\PackingCompletion;
use WebWMS\Inventory\Domain\PackingOrder;
use WebWMS\Inventory\Domain\PackingPackage;
use WebWMS\Inventory\Domain\PackingResult;
use WebWMS\Inventory\Domain\PickConfirmation;
use WebWMS\Inventory\Domain\PickConfirmationResult;
use WebWMS\Inventory\Domain\PickList;
use WebWMS\Inventory\Domain\PickListAssignment;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\PurchaseOrder;
use WebWMS\Inventory\Domain\PutawayConfirmation;
use WebWMS\Inventory\Domain\PutawayRequest;
use WebWMS\Inventory\Domain\PutawayResult;
use WebWMS\Inventory\Domain\PutawayStrategy;
use WebWMS\Inventory\Domain\ReplenishmentConfirmation;
use WebWMS\Inventory\Domain\ReplenishmentPolicy;
use WebWMS\Inventory\Domain\ReplenishmentRequest;
use WebWMS\Inventory\Domain\ReplenishmentResult;
use WebWMS\Inventory\Domain\ReturnInspection;
use WebWMS\Inventory\Domain\ReturnOrder;
use WebWMS\Inventory\Domain\ReturnReceipt;
use WebWMS\Inventory\Domain\ReturnResult;
use WebWMS\Inventory\Domain\Shipment;
use WebWMS\Inventory\Domain\ShipmentDispatch;
use WebWMS\Inventory\Domain\ShipmentLabel;
use WebWMS\Inventory\Domain\ShipmentLoading;
use WebWMS\Inventory\Domain\ShipmentResult;
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

    public function saveShipment(Shipment $shipment): void
    {
    }

    public function registerShipmentLabel(ShipmentLabel $label): ShipmentResult
    {
        return new ShipmentResult('labelled', 'TRACK-1');
    }

    public function dispatchShipment(ShipmentDispatch $dispatch): ShipmentResult
    {
        return new ShipmentResult('dispatched', 'TRACK-1');
    }

    public function saveLoadingManifest(LoadingManifest $manifest): void
    {
    }

    public function confirmShipmentLoading(ShipmentLoading $loading): LoadingResult
    {
        return new LoadingResult('loading', 1, 1);
    }

    public function completeLoadingManifest(LoadingCompletion $completion): LoadingResult
    {
        return new LoadingResult('completed', 1, 1);
    }

    public function saveReturnOrder(ReturnOrder $returnOrder): void
    {
    }

    public function receiveReturn(ReturnReceipt $receipt): ReturnResult
    {
        return new ReturnResult('in_progress', 'received');
    }

    public function inspectReturn(ReturnInspection $inspection): ReturnResult
    {
        return new ReturnResult('completed', 'processed', 'available', 1);
    }

    public function savePurchaseOrder(PurchaseOrder $purchaseOrder): void
    {
    }

    public function saveInboundDelivery(InboundDelivery $delivery): void
    {
    }

    public function receiveInboundDelivery(InboundReceipt $receipt): InboundResult
    {
        return new InboundResult('received', 'received');
    }

    public function inspectInboundReceipt(InboundInspection $inspection): InboundResult
    {
        return new InboundResult('completed', 'processed', 'available', 1);
    }

    public function savePutawayStrategy(PutawayStrategy $strategy): void
    {
    }

    public function createPutawayOrder(PutawayRequest $request): PutawayResult
    {
        return new PutawayResult('open', '018f6b7f-75d2-7c4e-8c33-31f91b1cf403', 1);
    }

    public function confirmPutaway(PutawayConfirmation $confirmation): PutawayResult
    {
        return new PutawayResult('completed', '018f6b7f-75d2-7c4e-8c33-31f91b1cf403', 1, 1);
    }

    public function saveReplenishmentPolicy(ReplenishmentPolicy $policy): void
    {
    }

    public function createReplenishmentOrder(ReplenishmentRequest $request): ReplenishmentResult
    {
        return new ReplenishmentResult('open', '018f6b7f-75d2-7c4e-8c33-31f91b1cf404', '018f6b7f-75d2-7c4e-8c33-31f91b1cf403', 1);
    }

    public function confirmReplenishment(ReplenishmentConfirmation $confirmation): ReplenishmentResult
    {
        return new ReplenishmentResult('completed', '018f6b7f-75d2-7c4e-8c33-31f91b1cf404', '018f6b7f-75d2-7c4e-8c33-31f91b1cf403', 1, 1);
    }
}
