<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Infrastructure\Persistence;

use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Inventory\Domain\AllocationTransitionType;
use WebWMS\Inventory\Domain\CycleCountExecution;
use WebWMS\Inventory\Domain\CycleCountPlan;
use WebWMS\Inventory\Domain\InboundDelivery;
use WebWMS\Inventory\Domain\InboundInspection;
use WebWMS\Inventory\Domain\InboundReceipt;
use WebWMS\Inventory\Domain\InboundResult;
use WebWMS\Inventory\Domain\InsufficientAvailableStockException;
use WebWMS\Inventory\Domain\InsufficientStockException;
use WebWMS\Inventory\Domain\InvalidSerialStockException;
use WebWMS\Inventory\Domain\InventoryCountApproval;
use WebWMS\Inventory\Domain\InventoryCountEntry;
use WebWMS\Inventory\Domain\InventoryCountPlan;
use WebWMS\Inventory\Domain\InventoryCountResult;
use WebWMS\Inventory\Domain\InventoryCountSubmission;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\LoadingCompletion;
use WebWMS\Inventory\Domain\LoadingManifest;
use WebWMS\Inventory\Domain\LoadingResult;
use WebWMS\Inventory\Domain\OutboundOrder;
use WebWMS\Inventory\Domain\OutboundOrderRelease;
use WebWMS\Inventory\Domain\OutboundOrderResult;
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
    public function __construct(
        private Connection $connection
    ) {
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
            $this->createZeroCrossingControl($connection, $posting, $currentQuantity, $newQuantity);

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
            $this->createZeroCrossingControl(
                $connection,
                $source,
                $sourceCurrent === false ? 0 : $sourceCurrent,
                $sourceQuantity,
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

    public function saveOutboundOrder(OutboundOrder $order): OutboundOrderResult
    {
        return $this->connection->transactional(
            function (Connection $connection) use ($order): OutboundOrderResult {
                if ($connection->fetchOne(
                    'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                    ['userId' => $order->createdBy()->value(), 'tenantId' => $order->tenantId()->value()],
                ) === false) {
                    throw new InventoryReferenceNotFoundException(
                        'The outbound order creator must exist in the tenant.',
                    );
                }

                $connection->insert('wms_outbound_order', [
                    'id' => $order->id()->value(),
                    'tenant_id' => $order->tenantId()->value(),
                    'order_number' => $order->orderNumber(),
                    'customer_reference' => $order->customerReference(),
                    'status' => 'imported',
                    'created_by' => $order->createdBy()->value(),
                    'created_at' => $order->createdAt()->format('Y-m-d H:i:s.u'),
                ]);
                foreach ($order->items() as $item) {
                    if ($connection->fetchOne(
                        'SELECT 1 FROM wms_product_reference WHERE id = :productId AND tenant_id = :tenantId',
                        [
                            'productId' => $item->productId()->value(),
                            'tenantId' => $order->tenantId()->value(),
                        ],
                    ) === false) {
                        throw new InventoryReferenceNotFoundException(
                            'Every outbound order product must exist in the tenant.',
                        );
                    }
                    $connection->insert('wms_outbound_order_item', [
                        'id' => $item->id()->value(),
                        'outbound_order_id' => $order->id()->value(),
                        'product_id' => $item->productId()->value(),
                        'requested_quantity' => $item->quantity(),
                    ]);
                }

                return new OutboundOrderResult('imported', count($order->items()));
            },
        );
    }

    public function releaseOutboundOrder(OutboundOrderRelease $release): OutboundOrderResult
    {
        return $this->connection->transactional(
            function (Connection $connection) use ($release): OutboundOrderResult {
                $order = $connection->fetchAssociative(
                    "SELECT id, order_number FROM wms_outbound_order WHERE id = :orderId "
                    . "AND tenant_id = :tenantId AND status = 'imported' FOR UPDATE",
                    [
                        'orderId' => $release->orderId()->value(),
                        'tenantId' => $release->tenantId()->value(),
                    ],
                );
                if ($order === false || $connection->fetchOne(
                    'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                    [
                        'userId' => $release->releasedBy()->value(),
                        'tenantId' => $release->tenantId()->value(),
                    ],
                ) === false) {
                    throw new InventoryReferenceNotFoundException(
                        'An imported outbound order and releasing user must exist in the tenant.',
                    );
                }
                $items = $connection->fetchAllAssociative(
                    'SELECT id, product_id, requested_quantity FROM wms_outbound_order_item '
                    . 'WHERE outbound_order_id = :orderId ORDER BY id FOR UPDATE',
                    ['orderId' => $release->orderId()->value()],
                );
                if (count($items) !== count($release->reservationIdsByItem())) {
                    throw new InventoryReferenceNotFoundException(
                        'Every outbound order item requires exactly one reservation ID.',
                    );
                }

                foreach ($items as $item) {
                    $itemId = (string) $item['id'];
                    $reservationId = $release->reservationIdsByItem()[$itemId] ?? null;
                    if ($reservationId === null) {
                        throw new InventoryReferenceNotFoundException(
                            'A reservation ID is missing for an outbound order item.',
                        );
                    }
                    $connection->insert('wms_stock_reservation', [
                        'id' => $reservationId->value(),
                        'tenant_id' => $release->tenantId()->value(),
                        'product_id' => $item['product_id'],
                        'order_reference' => $order['order_number'],
                        'requested_quantity' => $item['requested_quantity'],
                        'allocated_quantity' => 0,
                        'fulfilled_quantity' => 0,
                        'status' => 'open',
                        'created_by' => $release->releasedBy()->value(),
                        'created_at' => $release->releasedAt()->format('Y-m-d H:i:s.u'),
                        'updated_at' => $release->releasedAt()->format('Y-m-d H:i:s.u'),
                    ]);
                    $connection->update(
                        'wms_outbound_order_item',
                        ['reservation_id' => $reservationId->value()],
                        ['id' => $itemId],
                    );
                }
                $connection->update('wms_outbound_order', [
                    'status' => 'released',
                    'released_by' => $release->releasedBy()->value(),
                    'released_at' => $release->releasedAt()->format('Y-m-d H:i:s.u'),
                ], ['id' => $release->orderId()->value()]);

                return new OutboundOrderResult('released', count($items), count($items));
            },
        );
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
                new InventoryId((string) $task['allocation_id']),
                $confirmation->tenantId(),
                $confirmation->outcome() === PickOutcome::Picked ? AllocationTransitionType::Consume : AllocationTransitionType::Release,
                $confirmation->ledgerEntryId(),
                $confirmation->note(),
                $confirmation->confirmedBy(),
                $confirmation->confirmedAt(),
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

    public function savePurchaseOrder(PurchaseOrder $purchaseOrder): void
    {
        $this->connection->transactional(function (Connection $connection) use ($purchaseOrder): void {
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $purchaseOrder->createdBy()->value(), 'tenantId' => $purchaseOrder->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('The purchase order creator must exist in the tenant.');
            }
            $connection->insert('wms_purchase_order', [
                'id' => $purchaseOrder->id()->value(), 'tenant_id' => $purchaseOrder->tenantId()->value(),
                'code' => $purchaseOrder->code(), 'supplier_reference' => $purchaseOrder->supplierReference(),
                'status' => 'open', 'created_by' => $purchaseOrder->createdBy()->value(),
                'created_at' => $purchaseOrder->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $purchaseOrder->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            foreach ($purchaseOrder->items() as $item) {
                if ($connection->fetchOne(
                    'SELECT 1 FROM wms_product_reference WHERE id = :productId AND tenant_id = :tenantId',
                    ['productId' => $item->productId()->value(), 'tenantId' => $purchaseOrder->tenantId()->value()],
                ) === false) {
                    throw new InventoryReferenceNotFoundException('Every ordered product must exist in the tenant.');
                }
                $connection->insert('wms_purchase_order_item', [
                    'id' => $item->id()->value(), 'purchase_order_id' => $purchaseOrder->id()->value(),
                    'product_id' => $item->productId()->value(), 'ordered_quantity' => $item->orderedQuantity(),
                    'advised_quantity' => 0, 'received_quantity' => 0, 'status' => 'open',
                ]);
            }
        });
    }

    public function saveInboundDelivery(InboundDelivery $delivery): void
    {
        $this->connection->transactional(function (Connection $connection) use ($delivery): void {
            if ($connection->fetchOne(
                "SELECT 1 FROM wms_purchase_order o, wms_user_account u WHERE o.id = :orderId AND o.tenant_id = :tenantId AND o.status IN ('open', 'partially_advised') AND u.id = :userId AND u.tenant_id = :tenantId FOR UPDATE",
                ['orderId' => $delivery->purchaseOrderId()->value(), 'tenantId' => $delivery->tenantId()->value(), 'userId' => $delivery->createdBy()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An open purchase order and advice creator must exist in the tenant.');
            }
            $connection->insert('wms_inbound_delivery', [
                'id' => $delivery->id()->value(), 'tenant_id' => $delivery->tenantId()->value(),
                'purchase_order_id' => $delivery->purchaseOrderId()->value(), 'code' => $delivery->code(),
                'delivery_note' => $delivery->deliveryNote(), 'expected_at' => $delivery->expectedAt()->format('Y-m-d H:i:s.u'),
                'status' => 'advised', 'created_by' => $delivery->createdBy()->value(),
                'created_at' => $delivery->createdAt()->format('Y-m-d H:i:s.u'), 'updated_at' => $delivery->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            foreach ($delivery->lines() as $line) {
                $item = $connection->fetchAssociative(
                    'SELECT ordered_quantity, advised_quantity FROM wms_purchase_order_item WHERE id = :itemId AND purchase_order_id = :orderId FOR UPDATE',
                    ['itemId' => $line->purchaseOrderItemId()->value(), 'orderId' => $delivery->purchaseOrderId()->value()],
                );
                if ($item === false || (int) $item['advised_quantity'] + $line->advisedQuantity() > (int) $item['ordered_quantity']) {
                    throw new InsufficientAvailableStockException('The advice exceeds the open purchase order quantity.');
                }
                $newAdvised = (int) $item['advised_quantity'] + $line->advisedQuantity();
                $connection->insert('wms_inbound_delivery_line', [
                    'id' => $line->id()->value(), 'inbound_delivery_id' => $delivery->id()->value(),
                    'purchase_order_item_id' => $line->purchaseOrderItemId()->value(),
                    'advised_quantity' => $line->advisedQuantity(), 'status' => 'advised',
                ]);
                $connection->update('wms_purchase_order_item', [
                    'advised_quantity' => $newAdvised,
                    'status' => $newAdvised === (int) $item['ordered_quantity'] ? 'advised' : 'partially_advised',
                ], ['id' => $line->purchaseOrderItemId()->value()]);
            }
            $openItems = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_purchase_order_item WHERE purchase_order_id = :orderId AND advised_quantity < ordered_quantity',
                ['orderId' => $delivery->purchaseOrderId()->value()],
            );
            $connection->update('wms_purchase_order', [
                'status' => $openItems === 0 ? 'advised' : 'partially_advised',
                'updated_at' => $delivery->createdAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $delivery->purchaseOrderId()->value()]);
        });
    }

    public function receiveInboundDelivery(InboundReceipt $receipt): InboundResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($receipt): InboundResult {
            $line = $connection->fetchAssociative(
                "SELECT l.advised_quantity FROM wms_inbound_delivery_line l INNER JOIN wms_inbound_delivery d ON d.id = l.inbound_delivery_id WHERE l.id = :lineId AND d.id = :deliveryId AND d.tenant_id = :tenantId AND l.status = 'advised' AND d.status IN ('advised', 'receiving') FOR UPDATE",
                ['lineId' => $receipt->deliveryLineId()->value(), 'deliveryId' => $receipt->deliveryId()->value(), 'tenantId' => $receipt->tenantId()->value()],
            );
            if ($line === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $receipt->receivedBy()->value(), 'tenantId' => $receipt->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An advised line and receiver must exist in the tenant.');
            }
            $connection->insert('wms_inbound_receipt', [
                'id' => $receipt->id()->value(), 'inbound_delivery_line_id' => $receipt->deliveryLineId()->value(),
                'quantity' => (int) $line['advised_quantity'], 'status' => 'pending_quality',
                'received_by' => $receipt->receivedBy()->value(), 'received_at' => $receipt->receivedAt()->format('Y-m-d H:i:s.u'),
            ]);
            $connection->update('wms_inbound_delivery_line', ['status' => 'received'], ['id' => $receipt->deliveryLineId()->value()]);
            $remaining = (int) $connection->fetchOne(
                "SELECT COUNT(*) FROM wms_inbound_delivery_line WHERE inbound_delivery_id = :deliveryId AND status = 'advised'",
                ['deliveryId' => $receipt->deliveryId()->value()],
            );
            $deliveryStatus = $remaining === 0 ? 'received' : 'receiving';
            $connection->update('wms_inbound_delivery', [
                'status' => $deliveryStatus, 'updated_at' => $receipt->receivedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $receipt->deliveryId()->value()]);

            return new InboundResult($deliveryStatus, 'received');
        });
    }

    public function inspectInboundReceipt(InboundInspection $inspection): InboundResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($inspection): InboundResult {
            $receipt = $connection->fetchAssociative(
                "SELECT r.quantity, l.id line_id, l.purchase_order_item_id, d.id delivery_id, i.product_id, i.purchase_order_id, i.ordered_quantity, i.received_quantity FROM wms_inbound_receipt r INNER JOIN wms_inbound_delivery_line l ON l.id = r.inbound_delivery_line_id INNER JOIN wms_inbound_delivery d ON d.id = l.inbound_delivery_id INNER JOIN wms_purchase_order_item i ON i.id = l.purchase_order_item_id WHERE r.id = :receiptId AND r.status = 'pending_quality' AND d.tenant_id = :tenantId FOR UPDATE",
                ['receiptId' => $inspection->receiptId()->value(), 'tenantId' => $inspection->tenantId()->value()],
            );
            if ($receipt === false) {
                throw new InventoryReferenceNotFoundException('A pending inbound receipt must exist in the tenant.');
            }
            $posting = new StockPosting(
                $inspection->ledgerEntryId(),
                $inspection->tenantId(),
                new InventoryId((string) $receipt['product_id']),
                $inspection->locationId(),
                (int) $receipt['quantity'],
                'Inbound quality inspection',
                $inspection->inspectedBy(),
                $inspection->inspectedAt(),
                $inspection->dimensions(),
            );
            $this->assertReferencesExist($connection, $posting);
            $current = $connection->fetchOne(
                'SELECT quantity FROM wms_stock_balance WHERE tenant_id = :tenantId AND product_id = :productId AND location_id = :locationId AND stock_key = :stockKey FOR UPDATE',
                $this->postingKey($posting),
            );
            $newQuantity = ($current === false ? 0 : (int) $current) + $posting->quantityDelta();
            if ($posting->dimensions()->serialNumber() !== null && $newQuantity > 1) {
                throw new InvalidSerialStockException('An inbound serial number can only have a stock quantity of one.');
            }
            $this->persistBalance($connection, $posting, $newQuantity, $current !== false);
            $this->insertLedgerEntry($connection, $posting, $newQuantity, StockMovementType::InboundReceipt);
            foreach ($inspection->answers() as $position => $answer) {
                $connection->insert('wms_inbound_quality_answer', [
                    'receipt_id' => $inspection->receiptId()->value(), 'position' => $position + 1,
                    'question' => $answer->question(), 'passed' => $answer->passed() ? 1 : 0, 'note' => $answer->note(),
                ]);
            }
            $connection->update('wms_inbound_receipt', [
                'status' => 'inspected', 'quality_decision' => $inspection->decision()->value,
                'stock_status' => $inspection->dimensions()->status()->value, 'location_id' => $inspection->locationId()->value(),
                'ledger_entry_id' => $inspection->ledgerEntryId()->value(), 'inspected_by' => $inspection->inspectedBy()->value(),
                'inspected_at' => $inspection->inspectedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $inspection->receiptId()->value()]);
            $connection->update('wms_inbound_delivery_line', ['status' => 'processed'], ['id' => $receipt['line_id']]);
            $newReceived = (int) $receipt['received_quantity'] + (int) $receipt['quantity'];
            $connection->update('wms_purchase_order_item', [
                'received_quantity' => $newReceived,
                'status' => $newReceived === (int) $receipt['ordered_quantity'] ? 'completed' : 'partially_received',
            ], ['id' => $receipt['purchase_order_item_id']]);
            $openOrderItems = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_purchase_order_item WHERE purchase_order_id = :orderId AND received_quantity < ordered_quantity',
                ['orderId' => $receipt['purchase_order_id']],
            );
            $connection->update('wms_purchase_order', [
                'status' => $openOrderItems === 0 ? 'completed' : 'partially_received',
                'updated_at' => $inspection->inspectedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $receipt['purchase_order_id']]);
            $remaining = (int) $connection->fetchOne(
                "SELECT COUNT(*) FROM wms_inbound_delivery_line WHERE inbound_delivery_id = :deliveryId AND status <> 'processed'",
                ['deliveryId' => $receipt['delivery_id']],
            );
            $deliveryStatus = $remaining === 0 ? 'completed' : 'quality_check';
            $connection->update('wms_inbound_delivery', [
                'status' => $deliveryStatus, 'updated_at' => $inspection->inspectedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $receipt['delivery_id']]);

            return new InboundResult($deliveryStatus, 'processed', $inspection->dimensions()->status()->value, $newQuantity);
        });
    }

    public function savePutawayStrategy(PutawayStrategy $strategy): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_warehouse w, wms_user_account u WHERE w.id = :warehouseId AND w.tenant_id = :tenantId AND u.id = :userId AND u.tenant_id = :tenantId',
            ['warehouseId' => $strategy->warehouseId()->value(), 'tenantId' => $strategy->tenantId()->value(), 'userId' => $strategy->createdBy()->value()],
        ) === false) {
            throw new InventoryReferenceNotFoundException('The strategy warehouse and creator must exist in the tenant.');
        }
        $this->connection->insert('wms_putaway_strategy', [
            'id' => $strategy->id()->value(), 'tenant_id' => $strategy->tenantId()->value(),
            'warehouse_id' => $strategy->warehouseId()->value(), 'code' => $strategy->code(),
            'stock_status' => $strategy->stockStatus()->value, 'location_prefix' => $strategy->locationPrefix(),
            'priority' => $strategy->priority(), 'enabled' => 1, 'created_by' => $strategy->createdBy()->value(),
            'created_at' => $strategy->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function createPutawayOrder(PutawayRequest $request): PutawayResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($request): PutawayResult {
            $receipt = $connection->fetchAssociative(
                "SELECT r.quantity, r.location_id source_location_id, l.warehouse_id, i.product_id, g.stock_status, g.batch_number, g.serial_number, g.expires_at, g.stock_key FROM wms_inbound_receipt r INNER JOIN wms_inbound_delivery_line dl ON dl.id = r.inbound_delivery_line_id INNER JOIN wms_purchase_order_item i ON i.id = dl.purchase_order_item_id INNER JOIN wms_storage_location l ON l.id = r.location_id INNER JOIN wms_stock_ledger g ON g.id = r.ledger_entry_id WHERE r.id = :receiptId AND r.status = 'inspected' AND g.tenant_id = :tenantId FOR UPDATE",
                ['receiptId' => $request->inboundReceiptId()->value(), 'tenantId' => $request->tenantId()->value()],
            );
            if ($receipt === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $request->createdBy()->value(), 'tenantId' => $request->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An inspected inbound receipt and creator must exist in the tenant.');
            }
            $target = $connection->fetchAssociative(
                "SELECT s.id strategy_id, l.id target_location_id FROM wms_putaway_strategy s INNER JOIN wms_storage_location l ON l.warehouse_id = s.warehouse_id AND l.tenant_id = s.tenant_id AND l.code LIKE CONCAT(s.location_prefix, '%') WHERE s.tenant_id = :tenantId AND s.warehouse_id = :warehouseId AND s.stock_status = :stockStatus AND s.enabled = 1 AND l.putaway_enabled = 1 AND l.id <> :sourceLocationId AND (l.capacity_quantity = 0 OR (SELECT COALESCE(SUM(b.quantity), 0) FROM wms_stock_balance b WHERE b.location_id = l.id) + (SELECT COALESCE(SUM(po.quantity), 0) FROM wms_putaway_order po WHERE po.target_location_id = l.id AND po.status = 'open') + :quantity <= l.capacity_quantity) ORDER BY s.priority, CASE WHEN EXISTS (SELECT 1 FROM wms_stock_balance b2 WHERE b2.location_id = l.id AND b2.product_id = :productId AND b2.stock_key = :stockKey AND b2.quantity > 0) THEN 0 ELSE 1 END, l.putaway_priority, l.code LIMIT 1 FOR UPDATE",
                [
                    'tenantId' => $request->tenantId()->value(), 'warehouseId' => $receipt['warehouse_id'],
                    'stockStatus' => $receipt['stock_status'], 'sourceLocationId' => $receipt['source_location_id'],
                    'quantity' => (int) $receipt['quantity'], 'productId' => $receipt['product_id'], 'stockKey' => $receipt['stock_key'],
                ],
            );
            if ($target === false) {
                throw new InventoryReferenceNotFoundException('No enabled putaway strategy can provide a target location with sufficient capacity.');
            }
            $connection->insert('wms_putaway_order', [
                'id' => $request->orderId()->value(), 'tenant_id' => $request->tenantId()->value(),
                'inbound_receipt_id' => $request->inboundReceiptId()->value(), 'strategy_id' => $target['strategy_id'],
                'product_id' => $receipt['product_id'], 'source_location_id' => $receipt['source_location_id'],
                'target_location_id' => $target['target_location_id'], 'quantity' => (int) $receipt['quantity'],
                'stock_status' => $receipt['stock_status'], 'batch_number' => $receipt['batch_number'],
                'serial_number' => $receipt['serial_number'], 'expires_at' => $receipt['expires_at'],
                'status' => 'open', 'created_by' => $request->createdBy()->value(),
                'created_at' => $request->createdAt()->format('Y-m-d H:i:s.u'),
            ]);

            return new PutawayResult('open', (string) $target['target_location_id'], (int) $receipt['quantity']);
        });
    }

    public function confirmPutaway(PutawayConfirmation $confirmation): PutawayResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($confirmation): PutawayResult {
            $order = $connection->fetchAssociative(
                "SELECT * FROM wms_putaway_order WHERE id = :orderId AND tenant_id = :tenantId AND status = 'open' FOR UPDATE",
                ['orderId' => $confirmation->orderId()->value(), 'tenantId' => $confirmation->tenantId()->value()],
            );
            if ($order === false) {
                throw new InventoryReferenceNotFoundException('An open putaway order must exist in the tenant.');
            }
            $dimensions = StockDimensions::fromInput(
                (string) $order['stock_status'],
                $order['batch_number'] === null ? null : (string) $order['batch_number'],
                $order['serial_number'] === null ? null : (string) $order['serial_number'],
                $order['expires_at'] === null ? null : new DateTimeImmutable((string) $order['expires_at']),
            );
            $transfer = $this->transfer(new StockTransfer(
                $confirmation->transferId(),
                $confirmation->sourceLedgerId(),
                $confirmation->destinationLedgerId(),
                $confirmation->tenantId(),
                new InventoryId((string) $order['product_id']),
                new InventoryId((string) $order['source_location_id']),
                $dimensions,
                new InventoryId((string) $order['target_location_id']),
                $dimensions,
                (int) $order['quantity'],
                'Putaway order ' . $confirmation->orderId()->value(),
                $confirmation->confirmedBy(),
                $confirmation->confirmedAt(),
            ));
            $connection->update('wms_putaway_order', [
                'status' => 'completed', 'transfer_id' => $confirmation->transferId()->value(),
                'confirmed_by' => $confirmation->confirmedBy()->value(),
                'confirmed_at' => $confirmation->confirmedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $confirmation->orderId()->value()]);

            return new PutawayResult('completed', (string) $order['target_location_id'], (int) $order['quantity'], $transfer->destinationQuantity);
        });
    }

    public function saveReplenishmentPolicy(ReplenishmentPolicy $policy): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_warehouse w INNER JOIN wms_storage_location l ON l.warehouse_id = w.id '
            . 'INNER JOIN wms_product_reference p ON p.tenant_id = w.tenant_id '
            . 'INNER JOIN wms_user_account u ON u.tenant_id = w.tenant_id '
            . 'WHERE w.id = :warehouseId AND l.id = :targetLocationId AND p.id = :productId '
            . 'AND u.id = :userId AND w.tenant_id = :tenantId',
            [
                'warehouseId' => $policy->warehouseId()->value(),
                'targetLocationId' => $policy->targetLocationId()->value(),
                'productId' => $policy->productId()->value(),
                'userId' => $policy->createdBy()->value(),
                'tenantId' => $policy->tenantId()->value(),
            ],
        ) === false) {
            throw new InventoryReferenceNotFoundException('The replenishment warehouse, product, target location and creator must exist in the tenant.');
        }
        $this->connection->insert('wms_replenishment_policy', [
            'id' => $policy->id()->value(), 'tenant_id' => $policy->tenantId()->value(),
            'warehouse_id' => $policy->warehouseId()->value(), 'product_id' => $policy->productId()->value(),
            'target_location_id' => $policy->targetLocationId()->value(), 'code' => $policy->code(),
            'source_location_prefix' => $policy->sourceLocationPrefix(),
            'minimum_quantity' => $policy->minimumQuantity(), 'target_quantity' => $policy->targetQuantity(),
            'priority' => $policy->priority(), 'enabled' => 1, 'created_by' => $policy->createdBy()->value(),
            'created_at' => $policy->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function createReplenishmentOrder(ReplenishmentRequest $request): ReplenishmentResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($request): ReplenishmentResult {
            $policy = $connection->fetchAssociative(
                'SELECT * FROM wms_replenishment_policy WHERE id = :policyId AND tenant_id = :tenantId AND enabled = 1 FOR UPDATE',
                ['policyId' => $request->policyId()->value(), 'tenantId' => $request->tenantId()->value()],
            );
            if ($policy === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $request->createdBy()->value(), 'tenantId' => $request->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An enabled replenishment policy and creator must exist in the tenant.');
            }
            $targetQuantity = (int) $connection->fetchOne(
                "SELECT COALESCE(SUM(b.quantity), 0) - (SELECT COALESCE(SUM(a.quantity), 0) FROM wms_stock_allocation a WHERE a.tenant_id = :tenantId AND a.product_id = :productId AND a.location_id = :locationId AND a.status = 'active') FROM wms_stock_balance b WHERE b.tenant_id = :tenantId AND b.product_id = :productId AND b.location_id = :locationId AND b.stock_status = 'available'",
                ['tenantId' => $request->tenantId()->value(), 'productId' => $policy['product_id'], 'locationId' => $policy['target_location_id']],
            );
            $incomingQuantity = (int) $connection->fetchOne(
                "SELECT COALESCE(SUM(quantity), 0) FROM wms_replenishment_order WHERE policy_id = :policyId AND status = 'open'",
                ['policyId' => $request->policyId()->value()],
            );
            $effectiveQuantity = $targetQuantity + $incomingQuantity;
            if ($effectiveQuantity >= (int) $policy['minimum_quantity']) {
                throw new InsufficientAvailableStockException('The replenishment minimum has not been reached.');
            }
            $source = $connection->fetchAssociative(
                "SELECT b.*, l.code, b.quantity - (SELECT COALESCE(SUM(a.quantity), 0) FROM wms_stock_allocation a WHERE a.tenant_id = b.tenant_id AND a.product_id = b.product_id AND a.location_id = b.location_id AND a.stock_key = b.stock_key AND a.status = 'active') - (SELECT COALESCE(SUM(ro.quantity), 0) FROM wms_replenishment_order ro WHERE ro.source_location_id = b.location_id AND ro.stock_key = b.stock_key AND ro.status = 'open') available_quantity FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id WHERE b.tenant_id = :tenantId AND b.product_id = :productId AND b.stock_status = 'available' AND l.warehouse_id = :warehouseId AND l.code LIKE CONCAT(:sourcePrefix, '%') AND l.id <> :targetLocationId HAVING available_quantity > 0 ORDER BY CASE WHEN b.expires_at IS NULL THEN 1 ELSE 0 END, b.expires_at, l.putaway_priority, l.code LIMIT 1 FOR UPDATE",
                [
                    'tenantId' => $request->tenantId()->value(), 'productId' => $policy['product_id'],
                    'warehouseId' => $policy['warehouse_id'], 'sourcePrefix' => $policy['source_location_prefix'],
                    'targetLocationId' => $policy['target_location_id'],
                ],
            );
            if ($source === false) {
                throw new InsufficientAvailableStockException('No source location contains available stock for replenishment.');
            }
            $quantity = min((int) $policy['target_quantity'] - $effectiveQuantity, (int) $source['available_quantity']);
            $connection->insert('wms_replenishment_order', [
                'id' => $request->orderId()->value(), 'tenant_id' => $request->tenantId()->value(),
                'policy_id' => $request->policyId()->value(), 'product_id' => $policy['product_id'],
                'source_location_id' => $source['location_id'], 'target_location_id' => $policy['target_location_id'],
                'quantity' => $quantity, 'stock_key' => $source['stock_key'], 'stock_status' => $source['stock_status'],
                'batch_number' => $source['batch_number'], 'serial_number' => $source['serial_number'],
                'expires_at' => $source['expires_at'], 'status' => 'open',
                'created_by' => $request->createdBy()->value(), 'created_at' => $request->createdAt()->format('Y-m-d H:i:s.u'),
            ]);

            return new ReplenishmentResult('open', (string) $source['location_id'], (string) $policy['target_location_id'], $quantity);
        });
    }

    public function confirmReplenishment(ReplenishmentConfirmation $confirmation): ReplenishmentResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($confirmation): ReplenishmentResult {
            $order = $connection->fetchAssociative(
                "SELECT * FROM wms_replenishment_order WHERE id = :orderId AND tenant_id = :tenantId AND status = 'open' FOR UPDATE",
                ['orderId' => $confirmation->orderId()->value(), 'tenantId' => $confirmation->tenantId()->value()],
            );
            if ($order === false) {
                throw new InventoryReferenceNotFoundException('An open replenishment order must exist in the tenant.');
            }
            $dimensions = StockDimensions::fromInput(
                (string) $order['stock_status'],
                $order['batch_number'] === null ? null : (string) $order['batch_number'],
                $order['serial_number'] === null ? null : (string) $order['serial_number'],
                $order['expires_at'] === null ? null : new DateTimeImmutable((string) $order['expires_at']),
            );
            $transfer = $this->transfer(new StockTransfer(
                $confirmation->transferId(),
                $confirmation->sourceLedgerId(),
                $confirmation->destinationLedgerId(),
                $confirmation->tenantId(),
                new InventoryId((string) $order['product_id']),
                new InventoryId((string) $order['source_location_id']),
                $dimensions,
                new InventoryId((string) $order['target_location_id']),
                $dimensions,
                (int) $order['quantity'],
                'Replenishment order ' . $confirmation->orderId()->value(),
                $confirmation->confirmedBy(),
                $confirmation->confirmedAt(),
            ));
            $connection->update('wms_replenishment_order', [
                'status' => 'completed', 'transfer_id' => $confirmation->transferId()->value(),
                'confirmed_by' => $confirmation->confirmedBy()->value(),
                'confirmed_at' => $confirmation->confirmedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $confirmation->orderId()->value()]);

            return new ReplenishmentResult('completed', (string) $order['source_location_id'], (string) $order['target_location_id'], (int) $order['quantity'], $transfer->destinationQuantity);
        });
    }

    public function createInventoryCount(InventoryCountPlan $plan): InventoryCountResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($plan): InventoryCountResult {
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_warehouse w, wms_user_account u WHERE w.id = :warehouseId AND w.tenant_id = :tenantId AND u.id = :userId AND u.tenant_id = :tenantId FOR UPDATE',
                ['warehouseId' => $plan->warehouseId()->value(), 'tenantId' => $plan->tenantId()->value(), 'userId' => $plan->createdBy()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('The inventory warehouse and creator must exist in the tenant.');
            }
            if ($connection->fetchOne(
                "SELECT 1 FROM wms_inventory_count WHERE warehouse_id = :warehouseId AND location_prefix = :locationPrefix AND status IN ('open', 'counted') FOR UPDATE",
                ['warehouseId' => $plan->warehouseId()->value(), 'locationPrefix' => $plan->locationPrefix()],
            ) !== false) {
                throw new InventoryReferenceNotFoundException('An unfinished inventory count already covers this warehouse area.');
            }
            $connection->insert('wms_inventory_count', [
                'id' => $plan->id()->value(), 'tenant_id' => $plan->tenantId()->value(),
                'warehouse_id' => $plan->warehouseId()->value(), 'code' => $plan->code(),
                'location_prefix' => $plan->locationPrefix(), 'status' => 'open',
                'line_count' => 0, 'difference_count' => 0, 'created_by' => $plan->createdBy()->value(),
                'created_at' => $plan->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            $lineCount = $connection->executeStatement(
                "INSERT INTO wms_inventory_count_line (id, inventory_count_id, product_id, location_id, stock_key, stock_status, batch_number, serial_number, expires_at, expected_quantity) SELECT UUID(), :countId, b.product_id, b.location_id, b.stock_key, b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id WHERE b.tenant_id = :tenantId AND l.warehouse_id = :warehouseId AND l.code LIKE CONCAT(:locationPrefix, '%')",
                [
                    'countId' => $plan->id()->value(), 'tenantId' => $plan->tenantId()->value(),
                    'warehouseId' => $plan->warehouseId()->value(), 'locationPrefix' => $plan->locationPrefix(),
                ],
            );
            if ($lineCount === 0) {
                throw new InventoryReferenceNotFoundException('The inventory scope does not contain stock balances to count.');
            }
            $connection->update('wms_inventory_count', ['line_count' => $lineCount], ['id' => $plan->id()->value()]);

            return new InventoryCountResult('open', $lineCount, 0);
        });
    }

    public function recordInventoryCount(InventoryCountEntry $entry): InventoryCountResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($entry): InventoryCountResult {
            $line = $connection->fetchAssociative(
                "SELECT l.expected_quantity, c.line_count FROM wms_inventory_count_line l INNER JOIN wms_inventory_count c ON c.id = l.inventory_count_id WHERE l.id = :lineId AND c.id = :countId AND c.tenant_id = :tenantId AND c.status = 'open' FOR UPDATE",
                ['lineId' => $entry->lineId()->value(), 'countId' => $entry->countId()->value(), 'tenantId' => $entry->tenantId()->value()],
            );
            if ($line === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $entry->countedBy()->value(), 'tenantId' => $entry->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An open inventory line and counter must exist in the tenant.');
            }
            $difference = $entry->countedQuantity() - (int) $line['expected_quantity'];
            $connection->update('wms_inventory_count_line', [
                'counted_quantity' => $entry->countedQuantity(), 'difference_quantity' => $difference,
                'counted_by' => $entry->countedBy()->value(),
                'counted_at' => $entry->countedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $entry->lineId()->value()]);
            $differenceCount = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_inventory_count_line WHERE inventory_count_id = :countId AND difference_quantity <> 0',
                ['countId' => $entry->countId()->value()],
            );

            return new InventoryCountResult('open', (int) $line['line_count'], $differenceCount);
        });
    }

    public function submitInventoryCount(InventoryCountSubmission $submission): InventoryCountResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($submission): InventoryCountResult {
            $count = $connection->fetchAssociative(
                "SELECT * FROM wms_inventory_count WHERE id = :countId AND tenant_id = :tenantId AND status = 'open' FOR UPDATE",
                ['countId' => $submission->countId()->value(), 'tenantId' => $submission->tenantId()->value()],
            );
            if ($count === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $submission->submittedBy()->value(), 'tenantId' => $submission->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An open inventory count and submitter must exist in the tenant.');
            }
            $openLines = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_inventory_count_line WHERE inventory_count_id = :countId AND counted_quantity IS NULL',
                ['countId' => $submission->countId()->value()],
            );
            if ($openLines !== 0) {
                throw new InventoryReferenceNotFoundException('Every inventory line must be counted before submission.');
            }
            $differenceCount = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM wms_inventory_count_line WHERE inventory_count_id = :countId AND difference_quantity <> 0',
                ['countId' => $submission->countId()->value()],
            );
            $connection->update('wms_inventory_count', [
                'status' => 'counted', 'difference_count' => $differenceCount,
                'submitted_by' => $submission->submittedBy()->value(),
                'submitted_at' => $submission->submittedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $submission->countId()->value()]);

            return new InventoryCountResult('counted', (int) $count['line_count'], $differenceCount);
        });
    }

    public function approveInventoryCount(InventoryCountApproval $approval): InventoryCountResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($approval): InventoryCountResult {
            $count = $connection->fetchAssociative(
                "SELECT * FROM wms_inventory_count WHERE id = :countId AND tenant_id = :tenantId AND status = 'counted' FOR UPDATE",
                ['countId' => $approval->countId()->value(), 'tenantId' => $approval->tenantId()->value()],
            );
            if ($count === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $approval->approvedBy()->value(), 'tenantId' => $approval->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('A counted inventory and approver must exist in the tenant.');
            }
            if ($count['submitted_by'] === $approval->approvedBy()->value()) {
                throw new InventoryReferenceNotFoundException('The inventory count must be approved by a different user.');
            }
            $lines = $connection->fetchAllAssociative(
                'SELECT * FROM wms_inventory_count_line WHERE inventory_count_id = :countId AND difference_quantity <> 0 ORDER BY id FOR UPDATE',
                ['countId' => $approval->countId()->value()],
            );
            if (count($approval->ledgerEntryIds()) !== count($lines)) {
                throw new InventoryReferenceNotFoundException('Every inventory difference needs exactly one ledger entry ID.');
            }
            foreach ($lines as $line) {
                $lineId = (string) $line['id'];
                $ledgerEntryId = $approval->ledgerEntryIds()[$lineId] ?? null;
                if ($ledgerEntryId === null) {
                    throw new InventoryReferenceNotFoundException('A ledger entry ID is missing for an inventory difference.');
                }
                $currentQuantity = $connection->fetchOne(
                    'SELECT quantity FROM wms_stock_balance WHERE tenant_id = :tenantId AND product_id = :productId AND location_id = :locationId AND stock_key = :stockKey FOR UPDATE',
                    [
                        'tenantId' => $approval->tenantId()->value(), 'productId' => $line['product_id'],
                        'locationId' => $line['location_id'], 'stockKey' => $line['stock_key'],
                    ],
                );
                if ($currentQuantity === false || (int) $currentQuantity !== (int) $line['expected_quantity']) {
                    throw new InventoryReferenceNotFoundException('Stock changed after the inventory snapshot; the count cannot be approved.');
                }
                $dimensions = StockDimensions::fromInput(
                    (string) $line['stock_status'],
                    $line['batch_number'] === null ? null : (string) $line['batch_number'],
                    $line['serial_number'] === null ? null : (string) $line['serial_number'],
                    $line['expires_at'] === null ? null : new DateTimeImmutable((string) $line['expires_at']),
                );
                $posting = new StockPosting(
                    $ledgerEntryId,
                    $approval->tenantId(),
                    new InventoryId((string) $line['product_id']),
                    new InventoryId((string) $line['location_id']),
                    (int) $line['difference_quantity'],
                    'Inventory count ' . $approval->countId()->value(),
                    $approval->approvedBy(),
                    $approval->approvedAt(),
                    $dimensions,
                );
                $newQuantity = (int) $currentQuantity + $posting->quantityDelta();
                if ($newQuantity < $this->allocatedQuantity($connection, $posting)) {
                    throw new InsufficientAvailableStockException('The inventory adjustment would consume allocated stock.');
                }
                $this->persistBalance($connection, $posting, $newQuantity, true);
                $this->insertLedgerEntry($connection, $posting, $newQuantity, StockMovementType::InventoryAdjustment);
                $connection->update('wms_inventory_count_line', ['ledger_entry_id' => $ledgerEntryId->value()], ['id' => $lineId]);
            }
            $connection->update('wms_inventory_count', [
                'status' => 'completed', 'approved_by' => $approval->approvedBy()->value(),
                'approved_at' => $approval->approvedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $approval->countId()->value()]);

            return new InventoryCountResult('completed', (int) $count['line_count'], (int) $count['difference_count'], count($lines));
        });
    }

    public function saveCycleCountPlan(CycleCountPlan $plan): void
    {
        $this->connection->transactional(function (Connection $connection) use ($plan): void {
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_warehouse w, wms_user_account u WHERE w.id = :warehouseId '
                . 'AND w.tenant_id = :tenantId AND u.id = :userId AND u.tenant_id = :tenantId',
                [
                    'warehouseId' => $plan->warehouseId()->value(),
                    'tenantId' => $plan->tenantId()->value(),
                    'userId' => $plan->createdBy()->value(),
                ],
            ) === false) {
                throw new InventoryReferenceNotFoundException(
                    'The cycle count warehouse and creator must exist in the tenant.',
                );
            }

            $connection->insert('wms_cycle_count_plan', [
                'id' => $plan->id()->value(),
                'tenant_id' => $plan->tenantId()->value(),
                'warehouse_id' => $plan->warehouseId()->value(),
                'code' => $plan->code(),
                'location_prefix' => $plan->locationPrefix(),
                'interval_days' => $plan->intervalDays(),
                'next_due_at' => $plan->nextDueAt()->format('Y-m-d H:i:s.u'),
                'active' => 1,
                'created_by' => $plan->createdBy()->value(),
                'created_at' => $plan->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
        });
    }

    public function createDueCycleCount(CycleCountExecution $execution): InventoryCountResult
    {
        return $this->connection->transactional(
            function (Connection $connection) use ($execution): InventoryCountResult {
                $plan = $connection->fetchAssociative(
                    'SELECT * FROM wms_cycle_count_plan WHERE id = :planId AND tenant_id = :tenantId '
                    . 'AND active = 1 AND next_due_at <= :startedAt FOR UPDATE',
                    [
                        'planId' => $execution->planId()->value(),
                        'tenantId' => $execution->tenantId()->value(),
                        'startedAt' => $execution->startedAt()->format('Y-m-d H:i:s.u'),
                    ],
                );
                if ($plan === false || $connection->fetchOne(
                    'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                    [
                        'userId' => $execution->startedBy()->value(),
                        'tenantId' => $execution->tenantId()->value(),
                    ],
                ) === false) {
                    throw new InventoryReferenceNotFoundException(
                        'A due cycle count plan and executing user must exist in the tenant.',
                    );
                }
                if ($connection->fetchOne(
                    "SELECT 1 FROM wms_inventory_count WHERE warehouse_id = :warehouseId "
                    . "AND location_prefix = :locationPrefix AND status IN ('open', 'counted') FOR UPDATE",
                    [
                        'warehouseId' => $plan['warehouse_id'],
                        'locationPrefix' => $plan['location_prefix'],
                    ],
                ) !== false) {
                    throw new InventoryReferenceNotFoundException(
                        'An unfinished inventory count already covers this cycle count area.',
                    );
                }

                $connection->insert('wms_inventory_count', [
                    'id' => $execution->countId()->value(),
                    'tenant_id' => $execution->tenantId()->value(),
                    'warehouse_id' => $plan['warehouse_id'],
                    'code' => $execution->countCode(),
                    'location_prefix' => $plan['location_prefix'],
                    'count_type' => 'permanent',
                    'cycle_count_plan_id' => $execution->planId()->value(),
                    'status' => 'open',
                    'line_count' => 0,
                    'difference_count' => 0,
                    'created_by' => $execution->startedBy()->value(),
                    'created_at' => $execution->startedAt()->format('Y-m-d H:i:s.u'),
                ]);
                $lineCount = $connection->executeStatement(
                    "INSERT INTO wms_inventory_count_line (id, inventory_count_id, product_id, location_id, stock_key, stock_status, batch_number, serial_number, expires_at, expected_quantity) SELECT UUID(), :countId, b.product_id, b.location_id, b.stock_key, b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id WHERE b.tenant_id = :tenantId AND l.warehouse_id = :warehouseId AND l.code LIKE CONCAT(:locationPrefix, '%')",
                    [
                        'countId' => $execution->countId()->value(),
                        'tenantId' => $execution->tenantId()->value(),
                        'warehouseId' => $plan['warehouse_id'],
                        'locationPrefix' => $plan['location_prefix'],
                    ],
                );
                if ($lineCount === 0) {
                    throw new InventoryReferenceNotFoundException(
                        'The due cycle count scope does not contain stock balances to count.',
                    );
                }
                $connection->update(
                    'wms_inventory_count',
                    ['line_count' => $lineCount],
                    ['id' => $execution->countId()->value()],
                );
                $nextDueAt = $execution->startedAt()->add(
                    new DateInterval('P' . (int) $plan['interval_days'] . 'D'),
                );
                $connection->update('wms_cycle_count_plan', [
                    'last_started_at' => $execution->startedAt()->format('Y-m-d H:i:s.u'),
                    'next_due_at' => $nextDueAt->format('Y-m-d H:i:s.u'),
                ], ['id' => $execution->planId()->value()]);

                return new InventoryCountResult('open', $lineCount, 0);
            },
        );
    }

    public function saveReturnOrder(ReturnOrder $returnOrder): void
    {
        $this->connection->transactional(function (Connection $connection) use ($returnOrder): void {
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $returnOrder->createdBy()->value(), 'tenantId' => $returnOrder->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('The return creator must exist in the tenant.');
            }
            $connection->insert('wms_return_order', [
                'id' => $returnOrder->id()->value(), 'tenant_id' => $returnOrder->tenantId()->value(),
                'code' => $returnOrder->code(), 'order_reference' => $returnOrder->orderReference(),
                'status' => 'open', 'created_by' => $returnOrder->createdBy()->value(),
                'created_at' => $returnOrder->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $returnOrder->createdAt()->format('Y-m-d H:i:s.u'),
            ]);
            foreach ($returnOrder->items() as $item) {
                if ($connection->fetchOne(
                    'SELECT 1 FROM wms_product_reference WHERE id = :productId AND tenant_id = :tenantId',
                    ['productId' => $item->productId()->value(), 'tenantId' => $returnOrder->tenantId()->value()],
                ) === false) {
                    throw new InventoryReferenceNotFoundException('Every return product must exist in the tenant.');
                }
                $connection->insert('wms_return_item', [
                    'id' => $item->id()->value(), 'return_order_id' => $returnOrder->id()->value(),
                    'product_id' => $item->productId()->value(), 'expected_quantity' => $item->expectedQuantity(),
                    'reason' => $item->reason(), 'status' => 'expected',
                ]);
            }
        });
    }

    public function receiveReturn(ReturnReceipt $receipt): ReturnResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($receipt): ReturnResult {
            $item = $connection->fetchAssociative(
                "SELECT i.expected_quantity FROM wms_return_item i INNER JOIN wms_return_order r ON r.id = i.return_order_id WHERE i.id = :itemId AND r.id = :orderId AND r.tenant_id = :tenantId AND i.status = 'expected' AND r.status IN ('open', 'in_progress') FOR UPDATE",
                ['itemId' => $receipt->returnItemId()->value(), 'orderId' => $receipt->returnOrderId()->value(), 'tenantId' => $receipt->tenantId()->value()],
            );
            if ($item === false || $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $receipt->receivedBy()->value(), 'tenantId' => $receipt->tenantId()->value()],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An expected return item and receiver must exist in the tenant.');
            }
            $connection->insert('wms_return_receipt', [
                'id' => $receipt->id()->value(), 'return_item_id' => $receipt->returnItemId()->value(),
                'quantity' => (int) $item['expected_quantity'], 'status' => 'pending_inspection',
                'received_by' => $receipt->receivedBy()->value(),
                'received_at' => $receipt->receivedAt()->format('Y-m-d H:i:s.u'),
            ]);
            $connection->update('wms_return_item', ['status' => 'received'], ['id' => $receipt->returnItemId()->value()]);
            $connection->update('wms_return_order', [
                'status' => 'in_progress', 'updated_at' => $receipt->receivedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $receipt->returnOrderId()->value()]);

            return new ReturnResult('in_progress', 'received');
        });
    }

    public function inspectReturn(ReturnInspection $inspection): ReturnResult
    {
        return $this->connection->transactional(function (Connection $connection) use ($inspection): ReturnResult {
            $receipt = $connection->fetchAssociative(
                "SELECT rr.quantity, ri.id item_id, ri.product_id, ro.id return_order_id FROM wms_return_receipt rr INNER JOIN wms_return_item ri ON ri.id = rr.return_item_id INNER JOIN wms_return_order ro ON ro.id = ri.return_order_id WHERE rr.id = :receiptId AND rr.status = 'pending_inspection' AND ro.tenant_id = :tenantId FOR UPDATE",
                ['receiptId' => $inspection->receiptId()->value(), 'tenantId' => $inspection->tenantId()->value()],
            );
            if ($receipt === false) {
                throw new InventoryReferenceNotFoundException('A pending return receipt must exist in the tenant.');
            }
            $posting = new StockPosting(
                $inspection->ledgerEntryId(),
                $inspection->tenantId(),
                new InventoryId((string) $receipt['product_id']),
                $inspection->locationId(),
                (int) $receipt['quantity'],
                'Return inspection: ' . $inspection->note(),
                $inspection->inspectedBy(),
                $inspection->inspectedAt(),
                $inspection->dimensions(),
            );
            $this->assertReferencesExist($connection, $posting);
            $current = $connection->fetchOne(
                'SELECT quantity FROM wms_stock_balance WHERE tenant_id = :tenantId AND product_id = :productId AND location_id = :locationId AND stock_key = :stockKey FOR UPDATE',
                $this->postingKey($posting),
            );
            $newQuantity = ($current === false ? 0 : (int) $current) + $posting->quantityDelta();
            if ($posting->dimensions()->serialNumber() !== null && $newQuantity > 1) {
                throw new InvalidSerialStockException('A returned serial number can only have a stock quantity of one.');
            }
            $this->persistBalance($connection, $posting, $newQuantity, $current !== false);
            $this->insertLedgerEntry($connection, $posting, $newQuantity, StockMovementType::ReturnReceipt);
            $connection->update('wms_return_receipt', [
                'status' => 'inspected', 'quality_decision' => $inspection->decision()->value,
                'stock_status' => $inspection->dimensions()->status()->value,
                'location_id' => $inspection->locationId()->value(), 'ledger_entry_id' => $inspection->ledgerEntryId()->value(),
                'inspection_note' => $inspection->note(), 'inspected_by' => $inspection->inspectedBy()->value(),
                'inspected_at' => $inspection->inspectedAt()->format('Y-m-d H:i:s.u'),
            ], ['id' => $inspection->receiptId()->value()]);
            $connection->update('wms_return_item', ['status' => 'processed'], ['id' => $receipt['item_id']]);
            $remaining = (int) $connection->fetchOne(
                "SELECT COUNT(*) FROM wms_return_item i LEFT JOIN wms_return_receipt r ON r.return_item_id = i.id WHERE i.return_order_id = :orderId AND (r.id IS NULL OR r.status <> 'inspected')",
                ['orderId' => $receipt['return_order_id']],
            );
            $returnStatus = $remaining === 0 ? 'completed' : 'in_progress';
            $connection->update('wms_return_order', [
                'status' => $returnStatus, 'updated_at' => $inspection->inspectedAt()->format('Y-m-d H:i:s.u'),
                'completed_by' => $remaining === 0 ? $inspection->inspectedBy()->value() : null,
                'completed_at' => $remaining === 0 ? $inspection->inspectedAt()->format('Y-m-d H:i:s.u') : null,
            ], ['id' => $receipt['return_order_id']]);

            return new ReturnResult($returnStatus, 'processed', $inspection->dimensions()->status()->value, $newQuantity);
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
        $this->createZeroCrossingControl($connection, $posting, $physical, $newQuantity);

        return $newQuantity;
    }

    private function createZeroCrossingControl(
        Connection $connection,
        StockPosting $posting,
        int $previousQuantity,
        int $resultingQuantity,
    ): void {
        if ($previousQuantity <= 0 || $resultingQuantity !== 0) {
            return;
        }

        $location = $connection->fetchAssociative(
            'SELECT warehouse_id, code FROM wms_storage_location '
            . 'WHERE id = :locationId AND tenant_id = :tenantId',
            [
                'locationId' => $posting->locationId()->value(),
                'tenantId' => $posting->tenantId()->value(),
            ],
        );
        if ($location === false) {
            throw new InventoryReferenceNotFoundException(
                'The zero-crossing storage location must exist in the posting tenant.',
            );
        }
        if ($connection->fetchOne(
            "SELECT 1 FROM wms_inventory_count_line l INNER JOIN wms_inventory_count c "
            . "ON c.id = l.inventory_count_id WHERE c.tenant_id = :tenantId "
            . "AND c.count_type = 'zero_crossing' AND c.status IN ('open', 'counted') "
            . 'AND l.product_id = :productId AND l.location_id = :locationId AND l.stock_key = :stockKey',
            $this->postingKey($posting),
        ) !== false) {
            return;
        }

        $countId = (string) $connection->fetchOne('SELECT UUID()');
        $lineId = (string) $connection->fetchOne('SELECT UUID()');
        $connection->insert('wms_inventory_count', [
            'id' => $countId,
            'tenant_id' => $posting->tenantId()->value(),
            'warehouse_id' => $location['warehouse_id'],
            'code' => 'ZERO-' . $posting->id()->value(),
            'location_prefix' => $location['code'],
            'count_type' => 'zero_crossing',
            'trigger_ledger_entry_id' => $posting->id()->value(),
            'status' => 'open',
            'line_count' => 1,
            'difference_count' => 0,
            'created_by' => $posting->performedBy()->value(),
            'created_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
        ]);
        $connection->insert('wms_inventory_count_line', [
            'id' => $lineId,
            'inventory_count_id' => $countId,
            'product_id' => $posting->productId()->value(),
            'location_id' => $posting->locationId()->value(),
            'stock_key' => $posting->dimensions()->key(),
            ...$this->dimensionValues($posting),
            'expected_quantity' => 0,
        ]);
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
