<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

interface InventoryRepository
{
    public function saveProduct(ProductReference $product): void;
    public function saveWarehouse(Warehouse $warehouse): void;
    public function saveLocation(StorageLocation $location): void;
    public function post(StockPosting $posting): int;
    public function transfer(StockTransfer $transfer): StockTransferResult;
    public function saveReservation(StockReservation $reservation): void;
    public function allocate(StockAllocation $allocation): StockAllocationResult;
    public function transitionAllocation(StockAllocationTransition $transition): StockFulfillmentResult;
    public function savePickList(PickList $pickList): void;
    public function assignPickList(PickListAssignment $assignment): void;
    public function confirmPick(PickConfirmation $confirmation): PickConfirmationResult;
}
