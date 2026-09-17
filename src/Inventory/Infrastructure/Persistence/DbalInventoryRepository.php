<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Inventory\Domain\AllocationTransitionType;
use WebWMS\Inventory\Domain\InsufficientAvailableStockException;
use WebWMS\Inventory\Domain\InsufficientStockException;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\InvalidSerialStockException;
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
use WebWMS\Inventory\Domain\PickOutcome;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\Shipment;
use WebWMS\Inventory\Domain\ShipmentDispatch;
use WebWMS\Inventory\Domain\ShipmentLabel;
use WebWMS\Inventory\Domain\ShipmentResult;
use WebWMS\Inventory\Domain\ShipmentLoading;
use WebWMS\Inventory\Domain\StockAllocation;
use WebWMS\Inventory\Domain\StockAllocationResult;
use WebWMS\Inventory\Domain\StockAllocationTransition;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\StockFulfillmentResult;
use WebWMS\Inventory\Domain\StockMovementType;
use WebWMS\Inventory\Domain\StockPosting;
use WebWMS\Inventory\Domain\StockReservation;
use WebWMS\Inventory\Domain\StockTransfer;
use WebWMS\Inventory\Domain\StockTransferResult;
use WebWMS\Inventory\Domain\StorageLocation;
use WebWMS\Inventory\Domain\Warehouse;

final readonly class DbalInventoryRepository implements InventoryRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function saveProduct(ProductReference $product): void
    {
        $this->connection->insert('wms_product_reference', [
            'id' => $product->id()->value(),
            'tenant_id' => $product->tenantId()->value(),
            'sku' => $product->sku()->value(),
            'name' => $product->name(),
            'created_at' => $product->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function saveWarehouse(Warehouse $warehouse): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_site WHERE id = :siteId AND tenant_id = :tenantId',
            ['siteId' => $warehouse->siteId()->value(), 'tenantId' => $warehouse->tenantId()->value()],
        ) === false) {
            throw new InventoryReferenceNotFoundException('The site must exist in the warehouse tenant.');
        }

        $this->connection->insert('wms_warehouse', [
            'id' => $warehouse->id()->value(),
            'tenant_id' => $warehouse->tenantId()->value(),
            'site_id' => $warehouse->siteId()->value(),
            'code' => $warehouse->code(),
            'name' => $warehouse->name(),
            'created_at' => $warehouse->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function saveLocation(StorageLocation $location): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_warehouse WHERE id = :warehouseId AND tenant_id = :tenantId',
            [
                'warehouseId' => $location->warehouseId()->value(),
                'tenantId' => $location->tenantId()->value(),
            ],
        ) === false) {
            throw new InventoryReferenceNotFoundException('The warehouse must exist in the location tenant.');
        }

        $this->connection->insert('wms_storage_location', [
            'id' => $location->id()->value(),
            'tenant_id' => $location->tenantId()->value(),
            'warehouse_id' => $location->warehouseId()->value(),
            'code' => $location->code(),
            'created_at' => $location->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function post(StockPosting $posting): int
    {
        return $this->connection->transactional(function (Connection $connection) use ($posting): int {
            $this->assertReferencesExist($connection, $posting);
            $current = $connection->fetchOne(
                'SELECT quantity FROM wms_stock_balance '
                . 'WHERE tenant_id = :tenantId AND product_id = :productId '
                . 'AND location_id = :locationId AND stock_key = :stockKey '
                . 'FOR UPDATE',
                $this->postingKey($posting),
            );
            $currentQuantity = $current === false ? 0 : (int) $current;
            $newQuantity = $currentQuantity + $posting->quantityDelta();

            if ($newQuantity < 0) {
                throw new InsufficientStockException('The stock posting would create negative stock.');
            }

            if ($newQuantity < $this->allocatedQuantity($connection, $posting)) {
                throw new InsufficientAvailableStockException('The stock posting would consume allocated stock.');
            }

            if ($posting->dimensions()->serialNumber() !== null && $newQuantity > 1) {
                throw new InvalidSerialStockException('A serial number can only have a stock quantity of zero or one.');
            }

            $this->persistBalance($connection, $posting, $newQuantity, $current !== false);
            $this->insertLedgerEntry($connection, $posting, $newQuantity, StockMovementType::Posting);

            return $newQuantity;
        });
    }

    public function transfer(StockTransfer $transfer): StockTransferResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($transfer): StockTransferResult {
            $source = $transfer->sourcePosting();
            $destination = $transfer->destinationPosting();
            $this->assertReferencesExist($connection, $source);
            $this->assertReferencesExist($connection, $destination);

            $postings = [$source, $destination];
            usort(
                $postings,
                fn (StockPosting $left, StockPosting $right): int => $this->lockKey($left) <=> $this->lockKey($right),
            );

            /** @var array<string, int|false> $currentQuantities */
            $currentQuantities = [];
            foreach ($postings as $posting) {
                $current = $connection->fetchOne(
                    'SELECT quantity FROM wms_stock_balance '
                    . 'WHERE tenant_id = :tenantId AND product_id = :productId '
                    . 'AND location_id = :locationId AND stock_key = :stockKey '
                    . 'FOR UPDATE',
                    $this->postingKey($posting),
                );
                $currentQuantities[$this->lockKey($posting)] = $current === false ? false : (int) $current;
            }

            $sourceCurrent = $currentQuantities[$this->lockKey($source)];
            $destinationCurrent = $currentQuantities[$this->lockKey($destination)];
            $sourceQuantity = ($sourceCurrent === false ? 0 : $sourceCurrent) + $source->quantityDelta();
            $destinationQuantity = ($destinationCurrent === false ? 0 : $destinationCurrent)
                + $destination->quantityDelta();

            if ($sourceQuantity < 0) {
                throw new InsufficientStockException('The stock transfer source does not contain enough stock.');
            }

            if ($sourceQuantity < $this->allocatedQuantity($connection, $source)) {
                throw new InsufficientAvailableStockException('The stock transfer would move allocated stock.');
            }

            if ($destination->dimensions()->serialNumber() !== null && $destinationQuantity > 1) {
                throw new InvalidSerialStockException('The destination already contains this serial number.');
            }

            $this->persistBalance($connection, $source, $sourceQuantity, $sourceCurrent !== false);
            $this->persistBalance(
                $connection,
                $destination,
                $destinationQuantity,
                $destinationCurrent !== false,
            );
            $this->insertLedgerEntry(
                $connection,
                $source,
                $sourceQuantity,
                StockMovementType::TransferOut,
                $transfer->id(),
            );
            $this->insertLedgerEntry(
                $connection,
                $destination,
                $destinationQuantity,
                StockMovementType::TransferIn,
                $transfer->id(),
            );

            return new StockTransferResult($sourceQuantity, $destinationQuantity);
        });
    }

    public function saveReservation(StockReservation $reservation): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM wms_product_reference p, wms_user_account u '
            . 'WHERE p.id = :productId AND p.tenant_id = :tenantId '
            . 'AND u.id = :createdBy AND u.tenant_id = :tenantId',
            [
                'productId' => $reservation->productId()->value(),
                'tenantId' => $reservation->tenantId()->value(),
                'createdBy' => $reservation->createdBy()->value(),
            ],
        );
        if ($exists === false) {
            throw new InventoryReferenceNotFoundException('Product and user must exist in the reservation tenant.');
        }

        $this->connection->insert('wms_stock_reservation', [
            'id' => $reservation->id()->value(),
            'tenant_id' => $reservation->tenantId()->value(),
            'product_id' => $reservation->productId()->value(),
            'order_reference' => $reservation->orderReference(),
            'requested_quantity' => $reservation->requestedQuantity(),
            'allocated_quantity' => 0,
            'status' => 'open',
            'created_by' => $reservation->createdBy()->value(),
            'created_at' => $reservation->createdAt()->format('Y-m-d H:i:s.u'),
            'updated_at' => $reservation->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function allocate(StockAllocation $allocation): StockAllocationResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($allocation): StockAllocationResult {
            $reservation = $connection->fetchAssociative(
                'SELECT requested_quantity, allocated_quantity FROM wms_stock_reservation '
                . 'WHERE id = :reservationId AND tenant_id = :tenantId AND product_id = :productId '
                . "AND status IN ('open', 'partially_allocated') FOR UPDATE",
                [
                    'reservationId' => $allocation->reservationId()->value(),
                    'tenantId' => $allocation->tenantId()->value(),
                    'productId' => $allocation->productId()->value(),
                ],
            );
            if ($reservation === false) {
                throw new InventoryReferenceNotFoundException('An open reservation must exist for this tenant and product.');
            }

            $posting = new StockPosting(
                $allocation->id(),
                $allocation->tenantId(),
                $allocation->productId(),
                $allocation->locationId(),
                $allocation->quantity(),
                'Stock allocation',
                $allocation->createdBy(),
                $allocation->createdAt(),
                $allocation->dimensions(),
            );
            $this->assertReferencesExist($connection, $posting);
            $balance = $connection->fetchOne(
                'SELECT quantity FROM wms_stock_balance WHERE tenant_id = :tenantId '
                . 'AND product_id = :productId AND location_id = :locationId AND stock_key = :stockKey FOR UPDATE',
                $this->postingKey($posting),
            );
            $physical = $balance === false ? 0 : (int) $balance;
            $reserved = $this->allocatedQuantity($connection, $posting);
            $available = $physical - $reserved;
            $remaining = (int) $reservation['requested_quantity'] - (int) $reservation['allocated_quantity'];
            if ($allocation->quantity() > $available || $allocation->quantity() > $remaining) {
                throw new InsufficientAvailableStockException('The allocation exceeds available stock or reservation demand.');
            }

            $newAllocated = (int) $reservation['allocated_quantity'] + $allocation->quantity();
            $connection->insert('wms_stock_allocation', [
                'id' => $allocation->id()->value(),
                'reservation_id' => $allocation->reservationId()->value(),
                'tenant_id' => $allocation->tenantId()->value(),
                'product_id' => $allocation->productId()->value(),
                'location_id' => $allocation->locationId()->value(),
                'stock_key' => $allocation->dimensions()->key(),
                ...$this->allocationDimensionValues($allocation),
                'quantity' => $allocation->quantity(),
                'status' => 'active',
                'created_by' => $allocation->createdBy()->value(),
                'created_at' => $allocation->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            $connection->update('wms_stock_reservation', [
                'allocated_quantity' => $newAllocated,
                'status' => $newAllocated === (int) $reservation['requested_quantity'] ? 'allocated' : 'partially_allocated',
                'updated_at' => $allocation->createdAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $allocation->reservationId()->value()]);

            return new StockAllocationResult(
                $newAllocated,
                (int) $reservation['requested_quantity'] - $newAllocated,
                $available - $allocation->quantity(),
            );
        });
    }

    public function transitionAllocation(StockAllocationTransition $transition): StockFulfillmentResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($transition): StockFulfillmentResult {
            $allocation = $connection->fetchAssociative(
                'SELECT a.*, r.requested_quantity, r.allocated_quantity, r.fulfilled_quantity '
                . 'FROM wms_stock_allocation a INNER JOIN wms_stock_reservation r ON r.id = a.reservation_id '
                . "WHERE a.id = :allocationId AND a.tenant_id = :tenantId AND a.status = 'active' FOR UPDATE",
                ['allocationId' => $transition->allocationId()->value(), 'tenantId' => $transition->tenantId()->value()],
            );
            if ($allocation === false) {
                throw new InventoryReferenceNotFoundException('An active allocation must exist in the tenant.');
            }

            $userExists = $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $transition->performedBy()->value(), 'tenantId' => $transition->tenantId()->value()],
            );
            if ($userExists === false) {
                throw new InventoryReferenceNotFoundException('The fulfillment user must exist in the tenant.');
            }

            $physicalQuantity = $this->transitionPhysicalStock($connection, $allocation, $transition);
            $quantity = (int) $allocation['quantity'];
            $allocatedQuantity = (int) $allocation['allocated_quantity'] - $quantity;
            $fulfilledQuantity = (int) $allocation['fulfilled_quantity']
                + ($transition->type() === AllocationTransitionType::Consume ? $quantity : 0);
            $requestedQuantity = (int) $allocation['requested_quantity'];
            $reservationStatus = $this->reservationStatus(
                $requestedQuantity,
                $allocatedQuantity,
                $fulfilledQuantity,
            );

            $connection->update('wms_stock_allocation', [
                'status' => $transition->type()->value,
                'transitioned_by' => $transition->performedBy()->value(),
                'transitioned_at' => $transition->occurredAt()->format('Y-m-d H:i:s.u'),
                'transition_reason' => $transition->reason(),
            ], ['id' => $transition->allocationId()->value()]);
            $connection->update('wms_stock_reservation', [
                'allocated_quantity' => $allocatedQuantity,
                'fulfilled_quantity' => $fulfilledQuantity,
                'status' => $reservationStatus,
                'updated_at' => $transition->occurredAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $allocation['reservation_id']]);

            return new StockFulfillmentResult(
                $transition->type()->value,
                $reservationStatus,
                $physicalQuantity,
            );
        });
    }

    public function savePickList(PickList $pickList): void
    {
        $this->connection->transactional(function (Connection $connection) use ($pickList): void {
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $pickList->createdBy()->value(), 'tenantId' => $pickList->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('The pick list creator must exist in the tenant.');
            }
            $connection->insert('wms_pick_list', [
                'id' => $pickList->id()->value(), 'tenant_id' => $pickList->tenantId()->value(),
                'code' => $pickList->code(), 'status' => 'open', 'assigned_to' => null,
                'created_by' => $pickList->createdBy()->value(),
                'created_at' => $pickList->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $pickList->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            foreach ($pickList->allocationIds() as $sequence => $allocationId) {
                if ($connection->fetchOne(
                    "SELECT 1 FROM wms_stock_allocation WHERE id = :id AND tenant_id = :tenantId AND status = 'active' FOR UPDATE",
                    ['id' => $allocationId->value(), 'tenantId' => $pickList->tenantId()->value()],
                ) === false) {
                    throw new InventoryReferenceNotFoundException('Every pick task requires an active allocation.');
                }
                $connection->insert('wms_pick_task', [
                    'id' => $allocationId->value(), 'pick_list_id' => $pickList->id()->value(),
                    'allocation_id' => $allocationId->value(), 'sequence_number' => $sequence + 1,
                    'status' => 'open', 'confirmed_by' => null, 'confirmed_at' => null, 'note' => null,
                ]);
            }
        });
    }

    public function assignPickList(PickListAssignment $assignment): void
    {
        $this->connection->transactional(function (Connection $connection) use ($assignment): void {
            $users = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_user_account WHERE tenant_id = :tenantId AND id IN (:assignedTo, :assignedBy)',
                ['tenantId' => $assignment->tenantId()->value(), 'assignedTo' => $assignment->assignedTo()->value(), 'assignedBy' => $assignment->assignedBy()->value()],
            );
            $expectedUsers = $assignment->assignedTo()->value() === $assignment->assignedBy()->value() ? 1 : 2;
            if ($users !== $expectedUsers) {
                throw new InventoryReferenceNotFoundException('Both assignment users must exist in the tenant.');
            }
            $updated = $connection->executeStatement(
                "UPDATE wms_pick_list SET assigned_to = :assignedTo, assigned_by = :assignedBy, assigned_at = :assignedAt, status = 'assigned', updated_at = :assignedAt WHERE id = :id AND tenant_id = :tenantId AND status IN ('open', 'assigned')",
                ['assignedTo' => $assignment->assignedTo()->value(), 'assignedBy' => $assignment->assignedBy()->value(), 'assignedAt' => $assignment->assignedAt()->format('Y-m-d H:i:s.u'), 'id' => $assignment->pickListId()->value(), 'tenantId' => $assignment->tenantId()->value()],
            );
            if ($updated !== 1) {
                throw new InventoryReferenceNotFoundException('An assignable pick list must exist in the tenant.');
            }
        });
    }

    public function confirmPick(PickConfirmation $confirmation): PickConfirmationResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($confirmation): PickConfirmationResult {
            $task = $connection->fetchAssociative(
                "SELECT t.allocation_id, t.pick_list_id FROM wms_pick_task t INNER JOIN wms_pick_list l ON l.id = t.pick_list_id WHERE t.id = :taskId AND t.status = 'open' AND l.tenant_id = :tenantId AND l.assigned_to = :userId FOR UPDATE",
                ['taskId' => $confirmation->taskId()->value(), 'tenantId' => $confirmation->tenantId()->value(), 'userId' => $confirmation->confirmedBy()->value()],
            );
            if ($task === false) {
                throw new InventoryReferenceNotFoundException('An open task assigned to the confirming user must exist.');
            }
            $transition = new StockAllocationTransition(
                new InventoryId((string) $task['allocation_id']), $confirmation->tenantId(),
                $confirmation->outcome() === PickOutcome::Picked ? AllocationTransitionType::Consume : AllocationTransitionType::Release,
                $confirmation->ledgerEntryId(), $confirmation->note(), $confirmation->confirmedBy(), $confirmation->confirmedAt(),
            );
            $fulfillment = $this->transitionAllocation($transition);
            $connection->update('wms_pick_task', [
                'status' => $confirmation->outcome()->value, 'confirmed_by' => $confirmation->confirmedBy()->value(),
                'confirmed_at' => $confirmation->confirmedAt()->format('Y-m-d H:i:s.u'), 'note' => $confirmation->note(),
            ], ['id' => $confirmation->taskId()->value()]);
            $remaining = (int) $connection->fetchOne(
                "SELECT COUNT(*) FROM wms_pick_task WHERE pick_list_id = :pickListId AND status = 'open'",
                ['pickListId' => $task['pick_list_id']],
            );
            $listStatus = $remaining === 0 ? 'completed' : 'in_progress';
            $connection->update('wms_pick_list', ['status' => $listStatus, 'updated_at' => $confirmation->confirmedAt()->format('Y-m-d H:i:s.u')], ['id' => $task['pick_list_id']]);

            return new PickConfirmationResult($confirmation->outcome()->value, $listStatus, $fulfillment->reservationStatus);
        });
    }

    public function savePackingOrder(PackingOrder $order): void
    {
        $this->connection->transactional(function (Connection $connection) use ($order): void {
            $valid = $connection->fetchOne(
                "SELECT 1 FROM wms_pick_list l, wms_user_account u WHERE l.id = :pickListId AND l.tenant_id = :tenantId AND l.status = 'completed' AND u.id = :userId AND u.tenant_id = :tenantId FOR UPDATE",
                ['pickListId' => $order->pickListId()->value(), 'tenantId' => $order->tenantId()->value(), 'userId' => $order->createdBy()->value()],
            );
            if ($valid === false) {
                throw new InventoryReferenceNotFoundException('A completed pick list and creator must exist in the tenant.');
            }
            $connection->insert('wms_packing_order', [
                'id' => $order->id()->value(), 'tenant_id' => $order->tenantId()->value(),
                'pick_list_id' => $order->pickListId()->value(), 'code' => $order->code(), 'status' => 'open',
                'created_by' => $order->createdBy()->value(), 'created_at' => $order->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $order->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
        });
    }

    public function savePackingPackage(PackingPackage $package): void
    {
        $this->connection->transactional(function (Connection $connection) use ($package): void {
            $order = $connection->fetchAssociative(
                "SELECT pick_list_id FROM wms_packing_order WHERE id = :id AND tenant_id = :tenantId AND status IN ('open', 'packing') FOR UPDATE",
                ['id' => $package->packingOrderId()->value(), 'tenantId' => $package->tenantId()->value()],
            );
            if ($order === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $package->packedBy()->value(), 'tenantId' => $package->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An open packing order and packer must exist in the tenant.');
            }
            $connection->insert('wms_package', [
                'id' => $package->id()->value(), 'packing_order_id' => $package->packingOrderId()->value(),
                'package_number' => $package->packageNumber(), 'weight_grams' => $package->weightGrams(),
                'status' => 'sealed', 'packed_by' => $package->packedBy()->value(),
                'packed_at' => $package->packedAt()->format('Y-m-d H:i:s.u'),
            ]);
            foreach ($package->pickTaskIds() as $taskId) {
                $validTask = $connection->fetchOne(
                    "SELECT 1 FROM wms_pick_task WHERE id = :taskId AND pick_list_id = :pickListId AND status = 'picked'",
                    ['taskId' => $taskId->value(), 'pickListId' => $order['pick_list_id']],
                );
                if ($validTask === false) {
                    throw new InventoryReferenceNotFoundException('Every package item must reference a picked task from the source list.');
                }
                $connection->insert('wms_package_item', ['package_id' => $package->id()->value(), 'pick_task_id' => $taskId->value()]);
            }
            $connection->update('wms_packing_order', ['status' => 'packing', 'updated_at' => $package->packedAt()->format('Y-m-d H:i:s.u')], ['id' => $package->packingOrderId()->value()]);
        });
    }

    public function completePackingOrder(PackingCompletion $completion): PackingResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($completion): PackingResult {
            $order = $connection->fetchAssociative(
                "SELECT pick_list_id FROM wms_packing_order WHERE id = :id AND tenant_id = :tenantId AND status IN ('open', 'packing') FOR UPDATE",
                ['id' => $completion->packingOrderId()->value(), 'tenantId' => $completion->tenantId()->value()],
            );
            if ($order === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $completion->completedBy()->value(), 'tenantId' => $completion->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('A completable packing order and user must exist in the tenant.');
            }
            $expected = (int) $connection->fetchOne("SELECT COUNT(*) FROM wms_pick_task WHERE pick_list_id = :pickListId AND status = 'picked'", ['pickListId' => $order['pick_list_id']]);
            $packed = (int) $connection->fetchOne('SELECT COUNT(*) FROM wms_package_item i INNER JOIN wms_package p ON p.id = i.package_id WHERE p.packing_order_id = :orderId', ['orderId' => $completion->packingOrderId()->value()]);
            if ($expected === 0 || $expected !== $packed) {
                throw new InsufficientAvailableStockException('Every picked task must be packed exactly once before completion.');
            }
            $summary = $connection->fetchAssociative('SELECT COUNT(*) package_count, SUM(weight_grams) total_weight FROM wms_package WHERE packing_order_id = :orderId AND status = :status', ['orderId' => $completion->packingOrderId()->value(), 'status' => 'sealed']);
            if ($summary === false || (int) $summary['package_count'] === 0) {
                throw new InventoryReferenceNotFoundException('At least one sealed package is required.');
            }
            $connection->update('wms_packing_order', ['status' => 'completed', 'completed_by' => $completion->completedBy()->value(), 'completed_at' => $completion->completedAt()->format('Y-m-d H:i:s.u'), 'updated_at' => $completion->completedAt()->format('Y-m-d H:i:s.u')], ['id' => $completion->packingOrderId()->value()]);

            return new PackingResult('completed', (int) $summary['package_count'], (int) $summary['total_weight']);
        });
    }

    public function saveShipment(Shipment $shipment): void
    {
        $this->connection->transactional(function (Connection $connection) use ($shipment): void {
            $valid = $connection->fetchOne(
                "SELECT 1 FROM wms_packing_order o, wms_user_account u WHERE o.id = :packingOrderId AND o.tenant_id = :tenantId AND o.status = 'completed' AND u.id = :userId AND u.tenant_id = :tenantId FOR UPDATE",
                ['packingOrderId' => $shipment->packingOrderId()->value(), 'tenantId' => $shipment->tenantId()->value(), 'userId' => $shipment->createdBy()->value()],
            );
            if ($valid === false) {
                throw new InventoryReferenceNotFoundException('A completed packing order and creator must exist in the tenant.');
            }
            $connection->insert('wms_shipment', [
                'id' => $shipment->id()->value(), 'tenant_id' => $shipment->tenantId()->value(),
                'packing_order_id' => $shipment->packingOrderId()->value(), 'shipment_number' => $shipment->shipmentNumber(),
                'carrier' => $shipment->carrier(), 'service' => $shipment->service(), 'status' => 'prepared',
                'created_by' => $shipment->createdBy()->value(), 'created_at' => $shipment->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $shipment->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
        });
    }

    public function registerShipmentLabel(ShipmentLabel $label): ShipmentResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($label): ShipmentResult {
            $shipment = $connection->fetchAssociative(
                "SELECT id FROM wms_shipment WHERE id = :id AND tenant_id = :tenantId AND status = 'prepared' FOR UPDATE",
                ['id' => $label->shipmentId()->value(), 'tenantId' => $label->tenantId()->value()],
            );
            if ($shipment === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $label->registeredBy()->value(), 'tenantId' => $label->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('A prepared shipment and registering user must exist in the tenant.');
            }
            $connection->update('wms_shipment', [
                'status' => 'labelled', 'tracking_number' => $label->trackingNumber(),
                'label_reference' => $label->labelReference(), 'label_registered_by' => $label->registeredBy()->value(),
                'label_registered_at' => $label->registeredAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $label->registeredAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $label->shipmentId()->value()]);

            return new ShipmentResult('labelled', $label->trackingNumber());
        });
    }

    public function dispatchShipment(ShipmentDispatch $dispatch): ShipmentResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($dispatch): ShipmentResult {
            $shipment = $connection->fetchAssociative(
                "SELECT tracking_number FROM wms_shipment WHERE id = :id AND tenant_id = :tenantId AND status = 'labelled' FOR UPDATE",
                ['id' => $dispatch->shipmentId()->value(), 'tenantId' => $dispatch->tenantId()->value()],
            );
            if ($shipment === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $dispatch->dispatchedBy()->value(), 'tenantId' => $dispatch->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('A labelled shipment and dispatching user must exist in the tenant.');
            }
            $connection->update('wms_shipment', [
                'status' => 'dispatched', 'handover_reference' => $dispatch->handoverReference(),
                'dispatched_by' => $dispatch->dispatchedBy()->value(),
                'dispatched_at' => $dispatch->dispatchedAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $dispatch->dispatchedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $dispatch->shipmentId()->value()]);

            return new ShipmentResult('dispatched', (string) $shipment['tracking_number']);
        });
    }

    public function saveLoadingManifest(LoadingManifest $manifest): void
    {
        $this->connection->transactional(function (Connection $connection) use ($manifest): void {
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $manifest->createdBy()->value(), 'tenantId' => $manifest->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('The manifest creator must exist in the tenant.');
            }
            $connection->insert('wms_loading_manifest', [
                'id' => $manifest->id()->value(), 'tenant_id' => $manifest->tenantId()->value(),
                'code' => $manifest->code(), 'tour_reference' => $manifest->tourReference(),
                'vehicle_reference' => $manifest->vehicleReference(), 'status' => 'open',
                'created_by' => $manifest->createdBy()->value(),
                'created_at' => $manifest->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $manifest->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            foreach ($manifest->shipmentIds() as $shipmentId) {
                $shipment = $connection->fetchOne(
                    "SELECT 1 FROM wms_shipment WHERE id = :shipmentId AND tenant_id = :tenantId AND status = 'labelled' FOR UPDATE",
                    ['shipmentId' => $shipmentId->value(), 'tenantId' => $manifest->tenantId()->value()],
                );
                if ($shipment === false) {
                    throw new InventoryReferenceNotFoundException('Every manifest shipment must be labelled and belong to the tenant.');
                }
                $connection->insert('wms_loading_manifest_shipment', [
                    'manifest_id' => $manifest->id()->value(), 'shipment_id' => $shipmentId->value(),
                    'status' => 'pending',
                ]);
            }
        });
    }

    public function confirmShipmentLoading(ShipmentLoading $loading): LoadingResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($loading): LoadingResult {
            $row = $connection->fetchAssociative(
                "SELECT m.id FROM wms_loading_manifest m INNER JOIN wms_loading_manifest_shipment s ON s.manifest_id = m.id WHERE m.id = :manifestId AND m.tenant_id = :tenantId AND m.status IN ('open', 'loading') AND s.shipment_id = :shipmentId AND s.status = 'pending' FOR UPDATE",
                ['manifestId' => $loading->manifestId()->value(), 'tenantId' => $loading->tenantId()->value(), 'shipmentId' => $loading->shipmentId()->value()],
            );
            if ($row === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $loading->loadedBy()->value(), 'tenantId' => $loading->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('A pending manifest shipment and loader must exist in the tenant.');
            }
            $connection->update('wms_loading_manifest_shipment', [
                'status' => 'loaded', 'loaded_by' => $loading->loadedBy()->value(),
                'loaded_at' => $loading->loadedAt()->format('Y-m-d H:i:s.u'),
            ], ['manifest_id' => $loading->manifestId()->value(), 'shipment_id' => $loading->shipmentId()->value()]);
            $connection->update('wms_loading_manifest', [
                'status' => 'loading', 'updated_at' => $loading->loadedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $loading->manifestId()->value()]);
            $loaded = (int) $connection->fetchOne(
                "SELECT COUNT(*) FROM wms_loading_manifest_shipment WHERE manifest_id = :manifestId AND status = 'loaded'",
                ['manifestId' => $loading->manifestId()->value()],
            );
            $total = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_loading_manifest_shipment WHERE manifest_id = :manifestId',
                ['manifestId' => $loading->manifestId()->value()],
            );

            return new LoadingResult('loading', $loaded, $total);
        });
    }

    public function completeLoadingManifest(LoadingCompletion $completion): LoadingResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($completion): LoadingResult {
            $manifest = $connection->fetchAssociative(
                "SELECT code FROM wms_loading_manifest WHERE id = :manifestId AND tenant_id = :tenantId AND status IN ('open', 'loading') FOR UPDATE",
                ['manifestId' => $completion->manifestId()->value(), 'tenantId' => $completion->tenantId()->value()],
            );
            if ($manifest === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $completion->completedBy()->value(), 'tenantId' => $completion->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('A completable manifest and user must exist in the tenant.');
            }
            $total = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_loading_manifest_shipment WHERE manifest_id = :manifestId',
                ['manifestId' => $completion->manifestId()->value()],
            );
            $loaded = (int) $connection->fetchOne(
                "SELECT COUNT(*) FROM wms_loading_manifest_shipment WHERE manifest_id = :manifestId AND status = 'loaded'",
                ['manifestId' => $completion->manifestId()->value()],
            );
            if ($total === 0 || $loaded !== $total) {
                throw new InsufficientAvailableStockException('Every manifest shipment must be loaded before completion.');
            }
            $connection->executeStatement(
                "UPDATE wms_shipment s INNER JOIN wms_loading_manifest_shipment ms ON ms.shipment_id = s.id SET s.status = 'dispatched', s.handover_reference = :reference, s.dispatched_by = :userId, s.dispatched_at = :occurredAt, s.updated_at = :occurredAt WHERE ms.manifest_id = :manifestId AND s.tenant_id = :tenantId AND s.status = 'labelled'",
                [
                    'reference' => (string) $manifest['code'], 'userId' => $completion->completedBy()->value(),
                    'occurredAt' => $completion->completedAt()->format('Y-m-d H:i:s.u'),
                    'manifestId' => $completion->manifestId()->value(), 'tenantId' => $completion->tenantId()->value(),
                ],
            );
            $connection->update('wms_loading_manifest', [
                'status' => 'completed', 'completed_by' => $completion->completedBy()->value(),
                'completed_at' => $completion->completedAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $completion->completedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $completion->manifestId()->value()]);

            return new LoadingResult('completed', $loaded, $total);
        });
    }

    /** @param array<string, mixed> $allocation */
    private function transitionPhysicalStock(
        Connection $connection,
        array $allocation,
        StockAllocationTransition $transition,
    ): int {
        $posting = new StockPosting(
            $transition->ledgerEntryId() ?? $transition->allocationId(),
            $transition->tenantId(),
            new InventoryId((string) $allocation['product_id']),
            new InventoryId((string) $allocation['location_id']),
            -(int) $allocation['quantity'],
            $transition->reason(),
            $transition->performedBy(),
            $transition->occurredAt(),
            StockDimensions::fromInput(
                (string) $allocation['stock_status'],
                $allocation['batch_number'] === null ? null : (string) $allocation['batch_number'],
                $allocation['serial_number'] === null ? null : (string) $allocation['serial_number'],
                $allocation['expires_at'] === null ? null : new DateTimeImmutable((string) $allocation['expires_at']),
            ),
        );
        $balance = $connection->fetchOne(
            'SELECT quantity FROM wms_stock_balance WHERE tenant_id = :tenantId AND product_id = :productId '
            . 'AND location_id = :locationId AND stock_key = :stockKey FOR UPDATE',
            $this->postingKey($posting),
        );
        $physical = $balance === false ? 0 : (int) $balance;

        if ($transition->type() === AllocationTransitionType::Release) {
            return $physical;
        }

        $newQuantity = $physical + $posting->quantityDelta();
        if ($newQuantity < 0) {
            throw new InsufficientStockException('The allocated stock is no longer physically available.');
        }
        $this->persistBalance($connection, $posting, $newQuantity, true);
        $this->insertLedgerEntry(
            $connection,
            $posting,
            $newQuantity,
            StockMovementType::AllocationConsumption,
            allocationId: $transition->allocationId(),
            reservationId: new InventoryId((string) $allocation['reservation_id']),
        );

        return $newQuantity;
    }

    private function reservationStatus(int $requested, int $allocated, int $fulfilled): string
    {
        if ($fulfilled === $requested) {
            return 'fulfilled';
        }
        if ($allocated === 0) {
            return $fulfilled > 0 ? 'partially_fulfilled' : 'open';
        }

        return $allocated + $fulfilled === $requested ? 'allocated' : 'partially_allocated';
    }

    /** @return array{tenantId: string, productId: string, locationId: string, stockKey: string} */
    private function postingKey(StockPosting $posting): array
    {
        return [
            'tenantId' => $posting->tenantId()->value(),
            'productId' => $posting->productId()->value(),
            'locationId' => $posting->locationId()->value(),
            'stockKey' => $posting->dimensions()->key(),
        ];
    }

    /** @return array{tenant_id: string, product_id: string, location_id: string, stock_key: string} */
    private function balanceKey(StockPosting $posting): array
    {
        return [
            'tenant_id' => $posting->tenantId()->value(),
            'product_id' => $posting->productId()->value(),
            'location_id' => $posting->locationId()->value(),
            'stock_key' => $posting->dimensions()->key(),
        ];
    }

    /** @return array{stock_status: string, batch_number: ?string, serial_number: ?string, expires_at: ?string} */
    private function dimensionValues(StockPosting $posting): array
    {
        $dimensions = $posting->dimensions();

        return [
            'stock_status' => $dimensions->status()->value,
            'batch_number' => $dimensions->batchNumber(),
            'serial_number' => $dimensions->serialNumber(),
            'expires_at' => $dimensions->expiresAt()?->format('Y-m-d'),
        ];
    }

    /** @return array{stock_status: string, batch_number: ?string, serial_number: ?string, expires_at: ?string} */
    private function allocationDimensionValues(StockAllocation $allocation): array
    {
        $dimensions = $allocation->dimensions();

        return [
            'stock_status' => $dimensions->status()->value,
            'batch_number' => $dimensions->batchNumber(),
            'serial_number' => $dimensions->serialNumber(),
            'expires_at' => $dimensions->expiresAt()?->format('Y-m-d'),
        ];
    }

    private function allocatedQuantity(Connection $connection, StockPosting $posting): int
    {
        return (int) $connection->fetchOne(
            "SELECT COALESCE(SUM(quantity), 0) FROM wms_stock_allocation WHERE tenant_id = :tenantId "
            . "AND product_id = :productId AND location_id = :locationId AND stock_key = :stockKey AND status = 'active'",
            $this->postingKey($posting),
        );
    }

    private function lockKey(StockPosting $posting): string
    {
        return implode('|', [
            $posting->tenantId()->value(),
            $posting->productId()->value(),
            $posting->locationId()->value(),
            $posting->dimensions()->key(),
        ]);
    }

    private function persistBalance(
        Connection $connection,
        StockPosting $posting,
        int $quantity,
        bool $exists,
    ): void {
        if (!$exists) {
            $connection->insert('wms_stock_balance', [
                ...$this->balanceKey($posting),
                ...$this->dimensionValues($posting),
                'quantity' => $quantity,
                'updated_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
            ]);

            return;
        }

        $connection->update(
            'wms_stock_balance',
            [
                'quantity' => $quantity,
                'updated_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
            ],
            $this->balanceKey($posting),
        );
    }

    private function insertLedgerEntry(
        Connection $connection,
        StockPosting $posting,
        int $resultingQuantity,
        StockMovementType $movementType,
        ?InventoryId $transferId = null,
        ?InventoryId $allocationId = null,
        ?InventoryId $reservationId = null,
    ): void {
        $connection->insert('wms_stock_ledger', [
            'id' => $posting->id()->value(),
            ...$this->balanceKey($posting),
            ...$this->dimensionValues($posting),
            'quantity_delta' => $posting->quantityDelta(),
            'resulting_quantity' => $resultingQuantity,
            'movement_type' => $movementType->value,
            'transfer_id' => $transferId?->value(),
            'allocation_id' => $allocationId?->value(),
            'reservation_id' => $reservationId?->value(),
            'reason' => $posting->reason(),
            'performed_by' => $posting->performedBy()->value(),
            'occurred_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    private function assertReferencesExist(Connection $connection, StockPosting $posting): void
    {
        $result = $connection->fetchOne(
            'SELECT 1 FROM wms_product_reference p, wms_storage_location l, wms_user_account u '
            . 'WHERE p.id = :productId AND p.tenant_id = :tenantId '
            . 'AND l.id = :locationId AND l.tenant_id = :tenantId '
            . 'AND u.id = :performedBy AND u.tenant_id = :tenantId',
            [
                'tenantId' => $posting->tenantId()->value(),
                'productId' => $posting->productId()->value(),
                'locationId' => $posting->locationId()->value(),
                'performedBy' => $posting->performedBy()->value(),
            ],
        );

        if ($result === false) {
            throw new InventoryReferenceNotFoundException(
                'Product and storage location must exist in the posting tenant.',
            );
        }
    }
}
