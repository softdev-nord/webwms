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

    public function savePackingOrder(PackingOrder $order): void;

    public function savePackingPackage(PackingPackage $package): void;

    public function completePackingOrder(PackingCompletion $completion): PackingResult;

    public function saveShipment(Shipment $shipment): void;

    public function registerShipmentLabel(ShipmentLabel $label): ShipmentResult;

    public function dispatchShipment(ShipmentDispatch $dispatch): ShipmentResult;

    public function saveLoadingManifest(LoadingManifest $manifest): void;

    public function confirmShipmentLoading(ShipmentLoading $loading): LoadingResult;

    public function completeLoadingManifest(LoadingCompletion $completion): LoadingResult;

    public function saveReturnOrder(ReturnOrder $returnOrder): void;

    public function receiveReturn(ReturnReceipt $receipt): ReturnResult;

    public function inspectReturn(ReturnInspection $inspection): ReturnResult;

    public function savePurchaseOrder(PurchaseOrder $purchaseOrder): void;

    public function saveInboundDelivery(InboundDelivery $delivery): void;

    public function receiveInboundDelivery(InboundReceipt $receipt): InboundResult;

    public function inspectInboundReceipt(InboundInspection $inspection): InboundResult;

    public function savePutawayStrategy(PutawayStrategy $strategy): void;

    public function createPutawayOrder(PutawayRequest $request): PutawayResult;

    public function confirmPutaway(PutawayConfirmation $confirmation): PutawayResult;

    public function saveReplenishmentPolicy(ReplenishmentPolicy $policy): void;

    public function createReplenishmentOrder(ReplenishmentRequest $request): ReplenishmentResult;

    public function confirmReplenishment(ReplenishmentConfirmation $confirmation): ReplenishmentResult;

    public function createInventoryCount(InventoryCountPlan $plan): InventoryCountResult;

    public function recordInventoryCount(InventoryCountEntry $entry): InventoryCountResult;

    public function submitInventoryCount(InventoryCountSubmission $submission): InventoryCountResult;

    public function approveInventoryCount(InventoryCountApproval $approval): InventoryCountResult;

    public function saveCycleCountPlan(CycleCountPlan $plan): void;

    public function createDueCycleCount(CycleCountExecution $execution): InventoryCountResult;

    public function saveOutboundOrder(OutboundOrder $order): OutboundOrderResult;

    public function releaseOutboundOrder(OutboundOrderRelease $release): OutboundOrderResult;
}
