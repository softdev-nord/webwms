<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use Doctrine\DBAL\Connection;

final readonly class ApiV3QueryService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function products(string $tenantId, int $limit, ?string $cursor): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, sku, name, weight_grams, length_mm, width_mm, height_mm, created_at FROM wms_product_reference '
            . 'WHERE tenant_id = :tenantId AND (:cursorFilter IS NULL OR id > :cursorValue) '
            . 'ORDER BY id ASC LIMIT ' . $limit,
            ['tenantId' => $tenantId, 'cursorFilter' => $cursor, 'cursorValue' => $cursor ?? ''],
        );
    }

    /** @return list<array<string, mixed>> */
    public function warehouses(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT w.id, w.site_id, w.code, w.name, COUNT(l.id) location_count '
            . 'FROM wms_warehouse w LEFT JOIN wms_storage_location l ON l.warehouse_id = w.id '
            . 'WHERE w.tenant_id = :tenantId GROUP BY w.id, w.site_id, w.code, w.name ORDER BY w.code',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stock(string $tenantId, ?string $warehouseId, int $limit, ?string $cursor): array
    {
        return $this->connection->fetchAllAssociative(
            "SELECT CONCAT(b.product_id, '|', b.location_id, '|', b.stock_key) AS `cursor`, "
            . 'b.product_id, p.sku, b.location_id, l.code location_code, l.warehouse_id, '
            . 'b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity, '
            . "b.quantity - COALESCE((SELECT SUM(a.quantity) FROM wms_stock_allocation a WHERE a.tenant_id = b.tenant_id AND a.product_id = b.product_id AND a.location_id = b.location_id AND a.stock_key = b.stock_key AND a.status = 'active'), 0) available_quantity "
            . 'FROM wms_stock_balance b INNER JOIN wms_product_reference p ON p.id = b.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = b.location_id '
            . 'WHERE b.tenant_id = :tenantId '
            . 'AND (:warehouseFilter IS NULL OR l.warehouse_id = :warehouseId) '
            . "AND (:cursorFilter IS NULL OR CONCAT(b.product_id, '|', b.location_id, '|', b.stock_key) > :cursorValue) "
            . 'ORDER BY b.product_id, b.location_id, b.stock_key LIMIT ' . $limit,
            [
                'tenantId' => $tenantId,
                'warehouseFilter' => $warehouseId,
                'warehouseId' => $warehouseId ?? '',
                'cursorFilter' => $cursor,
                'cursorValue' => $cursor ?? '',
            ],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stockMovements(
        string $tenantId,
        StockMovementCriteria $criteria,
        int $limit,
        ?string $cursor
    ): array {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.product_id, p.sku, e.location_id, l.code location_code, '
            . 'e.stock_status, e.batch_number, e.serial_number, e.expires_at, '
            . 'e.quantity_delta, e.resulting_quantity, e.movement_type, e.transfer_id, '
            . 'e.allocation_id, e.reservation_id, e.reason, e.performed_by, e.occurred_at '
            . 'FROM wms_stock_ledger e INNER JOIN wms_product_reference p ON p.id = e.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = e.location_id '
            . 'WHERE e.tenant_id = :tenantId '
            . 'AND (:productFilter IS NULL OR e.product_id = :productId) '
            . 'AND (:locationFilter IS NULL OR e.location_id = :locationId) '
            . 'AND (:transferFilter IS NULL OR e.transfer_id = :transferId) '
            . 'AND (:movementTypeFilter IS NULL OR e.movement_type = :movementType) '
            . 'AND (:cursorFilter IS NULL OR e.id > :cursorValue) '
            . 'ORDER BY e.id LIMIT ' . $limit,
            [
                'tenantId' => $tenantId,
                'productFilter' => $criteria->productId,
                'productId' => $criteria->productId ?? '',
                'locationFilter' => $criteria->locationId,
                'locationId' => $criteria->locationId ?? '',
                'transferFilter' => $criteria->transferId,
                'transferId' => $criteria->transferId ?? '',
                'movementTypeFilter' => $criteria->movementType,
                'movementType' => $criteria->movementType ?? '',
                'cursorFilter' => $cursor,
                'cursorValue' => $cursor ?? '',
            ],
        );
    }

    /** @return list<array<string, mixed>> */
    public function erpConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_erp_connection WHERE tenant_id = :tenantId ORDER BY name, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function erpConnection(string $tenantId, string $connectionId): ?array
    {
        $connection = $this->connection->fetchAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_erp_connection WHERE id = :connectionId AND tenant_id = :tenantId',
            ['connectionId' => $connectionId, 'tenantId' => $tenantId],
        );

        return $connection === false ? null : $connection;
    }

    /** @return array<string, mixed>|null */
    public function outboundOrder(string $tenantId, string $orderId): ?array
    {
        $order = $this->connection->fetchAssociative(
            'SELECT o.id, o.order_number, o.customer_reference, o.status, o.created_at, o.released_at, '
            . 'l.id pick_list_id, l.code pick_list_code, l.status pick_list_status '
            . 'FROM wms_outbound_order o LEFT JOIN wms_pick_list l ON l.outbound_order_id = o.id '
            . 'WHERE o.id = :orderId AND o.tenant_id = :tenantId',
            ['orderId' => $orderId, 'tenantId' => $tenantId],
        );
        if ($order === false) {
            return null;
        }
        $order['items'] = $this->connection->fetchAllAssociative(
            'SELECT i.id, i.product_id, p.sku, i.requested_quantity, i.reservation_id, '
            . 'r.status reservation_status, r.allocated_quantity, r.fulfilled_quantity '
            . 'FROM wms_outbound_order_item i INNER JOIN wms_product_reference p ON p.id = i.product_id '
            . 'LEFT JOIN wms_stock_reservation r ON r.id = i.reservation_id '
            . 'WHERE i.outbound_order_id = :orderId ORDER BY i.id',
            ['orderId' => $orderId],
        );

        return $order;
    }

    /** @return list<array<string, mixed>> */
    public function outboundOrders(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT o.id, o.order_number, o.customer_reference, o.status, o.created_at, o.released_at, '
            . 'COUNT(i.id) item_count, COALESCE(SUM(i.requested_quantity), 0) requested_quantity, '
            . 'l.id pick_list_id, l.code pick_list_code, l.status pick_list_status '
            . 'FROM wms_outbound_order o LEFT JOIN wms_outbound_order_item i ON i.outbound_order_id = o.id '
            . 'LEFT JOIN wms_pick_list l ON l.outbound_order_id = o.id '
            . 'WHERE o.tenant_id = :tenantId '
            . 'GROUP BY o.id, o.order_number, o.customer_reference, o.status, o.created_at, o.released_at, '
            . 'l.id, l.code, l.status ORDER BY o.created_at DESC, o.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function availableStockForProduct(string $tenantId, string $productId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT b.location_id, l.code location_code, b.stock_status, b.batch_number, b.serial_number, '
            . 'b.expires_at, b.quantity - COALESCE(SUM(a.quantity), 0) available_quantity '
            . 'FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id '
            . "LEFT JOIN wms_stock_allocation a ON a.tenant_id = b.tenant_id AND a.product_id = b.product_id AND a.location_id = b.location_id AND a.stock_key = b.stock_key AND a.status = 'active' "
            . 'WHERE b.tenant_id = :tenantId AND b.product_id = :productId '
            . 'GROUP BY b.location_id, l.code, b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity '
            . 'HAVING available_quantity > 0 ORDER BY l.code, b.stock_key',
            ['tenantId' => $tenantId, 'productId' => $productId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function pickLists(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT l.id, l.outbound_order_id, o.order_number, l.code, l.status, l.assigned_to, '
            . 'u.display_name assigned_to_name, COUNT(t.id) task_count, '
            . "COALESCE(SUM(CASE WHEN t.status <> 'open' THEN 1 ELSE 0 END), 0) completed_task_count, l.created_at "
            . 'FROM wms_pick_list l INNER JOIN wms_outbound_order o ON o.id = l.outbound_order_id '
            . 'LEFT JOIN wms_user_account u ON u.id = l.assigned_to '
            . 'LEFT JOIN wms_pick_task t ON t.pick_list_id = l.id '
            . 'WHERE l.tenant_id = :tenantId '
            . 'GROUP BY l.id, l.outbound_order_id, o.order_number, l.code, l.status, l.assigned_to, '
            . 'u.display_name, l.created_at ORDER BY l.created_at DESC, l.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function reservation(string $tenantId, string $reservationId): ?array
    {
        $reservation = $this->connection->fetchAssociative(
            'SELECT r.id, r.product_id, p.sku, r.order_reference, r.requested_quantity, '
            . 'r.allocated_quantity, r.fulfilled_quantity, r.status, r.created_at, r.updated_at '
            . 'FROM wms_stock_reservation r INNER JOIN wms_product_reference p ON p.id = r.product_id '
            . 'WHERE r.id = :reservationId AND r.tenant_id = :tenantId',
            ['reservationId' => $reservationId, 'tenantId' => $tenantId],
        );
        if ($reservation === false) {
            return null;
        }
        $reservation['allocations'] = $this->connection->fetchAllAssociative(
            'SELECT id, location_id, stock_status, batch_number, serial_number, expires_at, quantity, status '
            . 'FROM wms_stock_allocation WHERE reservation_id = :reservationId AND tenant_id = :tenantId '
            . 'ORDER BY created_at, id',
            ['reservationId' => $reservationId, 'tenantId' => $tenantId],
        );

        return $reservation;
    }

    /** @return list<string> */
    public function pickableAllocationIds(string $tenantId, string $orderId): array
    {
        $ids = $this->connection->fetchFirstColumn(
            'SELECT a.id FROM wms_stock_allocation a '
            . 'INNER JOIN wms_stock_reservation r ON r.id = a.reservation_id '
            . 'INNER JOIN wms_outbound_order_item i ON i.reservation_id = r.id '
            . 'INNER JOIN wms_outbound_order o ON o.id = i.outbound_order_id '
            . "WHERE o.id = :orderId AND o.tenant_id = :tenantId AND o.status = 'released' AND a.status = 'active' "
            . 'AND NOT EXISTS (SELECT 1 FROM wms_outbound_order_item pending '
            . 'LEFT JOIN wms_stock_reservation pending_reservation ON pending_reservation.id = pending.reservation_id '
            . 'WHERE pending.outbound_order_id = o.id AND (pending_reservation.id IS NULL OR pending_reservation.allocated_quantity < pending.requested_quantity)) '
            . 'ORDER BY i.id, a.created_at, a.id',
            ['orderId' => $orderId, 'tenantId' => $tenantId],
        );

        $allocationIds = [];
        foreach ($ids as $id) {
            if (!is_string($id)) {
                throw new \LogicException('The allocation ID projection is invalid.');
            }
            $allocationIds[] = $id;
        }

        return $allocationIds;
    }

    /** @return array<string, mixed>|null */
    public function pickList(string $tenantId, string $pickListId): ?array
    {
        $pickList = $this->connection->fetchAssociative(
            'SELECT l.id, l.outbound_order_id, o.order_number, l.code, l.status, l.assigned_to, '
            . 'l.assigned_by, l.assigned_at, l.created_by, l.created_at, l.updated_at, '
            . 'po.id packing_order_id, po.code packing_order_code, po.status packing_order_status '
            . 'FROM wms_pick_list l LEFT JOIN wms_outbound_order o ON o.id = l.outbound_order_id '
            . 'LEFT JOIN wms_packing_order po ON po.pick_list_id = l.id '
            . 'WHERE l.id = :pickListId AND l.tenant_id = :tenantId',
            ['pickListId' => $pickListId, 'tenantId' => $tenantId],
        );
        if ($pickList === false) {
            return null;
        }
        $pickList['tasks'] = $this->connection->fetchAllAssociative(
            'SELECT t.id, t.sequence_number, t.status, t.confirmed_by, t.confirmed_at, t.note, '
            . 'a.id allocation_id, a.product_id, p.sku, a.location_id, l.code location_code, '
            . 'a.stock_status, a.batch_number, a.serial_number, a.expires_at, a.quantity '
            . 'FROM wms_pick_task t INNER JOIN wms_stock_allocation a ON a.id = t.allocation_id '
            . 'INNER JOIN wms_product_reference p ON p.id = a.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = a.location_id '
            . 'WHERE t.pick_list_id = :pickListId ORDER BY t.sequence_number',
            ['pickListId' => $pickListId],
        );

        return $pickList;
    }

    /** @return array<string, mixed>|null */
    public function packingOrder(string $tenantId, string $packingOrderId): ?array
    {
        $order = $this->connection->fetchAssociative(
            'SELECT o.id, o.pick_list_id, l.outbound_order_id, o.code, o.status, o.created_by, '
            . 'o.created_at, o.updated_at, o.completed_by, o.completed_at, '
            . 's.id shipment_id, s.shipment_number, s.status shipment_status '
            . 'FROM wms_packing_order o INNER JOIN wms_pick_list l ON l.id = o.pick_list_id '
            . 'LEFT JOIN wms_shipment s ON s.packing_order_id = o.id '
            . 'WHERE o.id = :packingOrderId AND o.tenant_id = :tenantId',
            ['packingOrderId' => $packingOrderId, 'tenantId' => $tenantId],
        );
        if ($order === false) {
            return null;
        }
        $packages = $this->connection->fetchAllAssociative(
            'SELECT id, package_number, weight_grams, length_mm, width_mm, height_mm, status, packed_by, packed_at '
            . 'FROM wms_package WHERE packing_order_id = :packingOrderId ORDER BY packed_at, id',
            ['packingOrderId' => $packingOrderId],
        );
        foreach ($packages as &$package) {
            if (!is_string($package['id'] ?? null)) {
                throw new \LogicException('The package projection is invalid.');
            }
            $package['pickTaskIds'] = $this->connection->fetchFirstColumn(
                'SELECT pick_task_id FROM wms_package_item WHERE package_id = :packageId ORDER BY pick_task_id',
                ['packageId' => $package['id']],
            );
        }
        unset($package);
        $order['packages'] = $packages;

        return $order;
    }

    /** @return list<array<string, mixed>> */
    public function packingOrders(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT p.id, p.pick_list_id, l.code pick_list_code, l.outbound_order_id, o.order_number, '
            . 'p.code, p.status, COUNT(pkg.id) package_count, COALESCE(SUM(pkg.weight_grams), 0) total_weight_grams, '
            . 's.id shipment_id, s.shipment_number, p.created_at '
            . 'FROM wms_packing_order p INNER JOIN wms_pick_list l ON l.id = p.pick_list_id '
            . 'INNER JOIN wms_outbound_order o ON o.id = l.outbound_order_id '
            . 'LEFT JOIN wms_package pkg ON pkg.packing_order_id = p.id '
            . 'LEFT JOIN wms_shipment s ON s.packing_order_id = p.id '
            . 'WHERE p.tenant_id = :tenantId '
            . 'GROUP BY p.id, p.pick_list_id, l.code, l.outbound_order_id, o.order_number, p.code, p.status, '
            . 's.id, s.shipment_number, p.created_at ORDER BY p.created_at DESC, p.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function packablePickTasks(string $tenantId, string $packingOrderId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT t.id, t.sequence_number, a.product_id, p.sku, a.quantity, l.code location_code '
            . 'FROM wms_packing_order po INNER JOIN wms_pick_list pl ON pl.id = po.pick_list_id '
            . 'INNER JOIN wms_pick_task t ON t.pick_list_id = pl.id '
            . 'INNER JOIN wms_stock_allocation a ON a.id = t.allocation_id '
            . 'INNER JOIN wms_product_reference p ON p.id = a.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = a.location_id '
            . "WHERE po.id = :packingOrderId AND po.tenant_id = :tenantId AND t.status = 'picked' "
            . 'AND NOT EXISTS (SELECT 1 FROM wms_package_item pi WHERE pi.pick_task_id = t.id) '
            . 'ORDER BY t.sequence_number, t.id',
            ['packingOrderId' => $packingOrderId, 'tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function shipment(string $tenantId, string $shipmentId): ?array
    {
        $shipment = $this->connection->fetchAssociative(
            'SELECT s.id, s.packing_order_id, p.pick_list_id, l.outbound_order_id, '
            . 's.shipment_number, s.carrier, s.service, s.status, s.tracking_number, '
            . 's.label_reference, s.handover_reference, s.created_by, s.created_at, '
            . 's.updated_at, s.label_registered_by, s.label_registered_at, '
            . 's.dispatched_by, s.dispatched_at '
            . 'FROM wms_shipment s INNER JOIN wms_packing_order p ON p.id = s.packing_order_id '
            . 'INNER JOIN wms_pick_list l ON l.id = p.pick_list_id '
            . 'WHERE s.id = :shipmentId AND s.tenant_id = :tenantId',
            ['shipmentId' => $shipmentId, 'tenantId' => $tenantId],
        );

        return $shipment === false ? null : $shipment;
    }

    /** @return list<array<string, mixed>> */
    public function shipments(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT s.id, s.packing_order_id, p.code packing_order_code, s.shipment_number, s.carrier, '
            . 's.service, s.status, s.tracking_number, s.label_reference, s.handover_reference, s.created_at '
            . 'FROM wms_shipment s INNER JOIN wms_packing_order p ON p.id = s.packing_order_id '
            . 'WHERE s.tenant_id = :tenantId ORDER BY s.created_at DESC, s.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function loadingManifest(string $tenantId, string $manifestId): ?array
    {
        $manifest = $this->connection->fetchAssociative(
            'SELECT id, code, tour_reference, vehicle_reference, status, created_by, '
            . 'created_at, updated_at, completed_by, completed_at '
            . 'FROM wms_loading_manifest WHERE id = :manifestId AND tenant_id = :tenantId',
            ['manifestId' => $manifestId, 'tenantId' => $tenantId],
        );
        if ($manifest === false) {
            return null;
        }
        $manifest['shipments'] = $this->connection->fetchAllAssociative(
            'SELECT ms.shipment_id, s.shipment_number, s.carrier, s.service, s.tracking_number, '
            . 'ms.status, ms.loaded_by, ms.loaded_at '
            . 'FROM wms_loading_manifest_shipment ms '
            . 'INNER JOIN wms_shipment s ON s.id = ms.shipment_id '
            . 'WHERE ms.manifest_id = :manifestId ORDER BY s.shipment_number, s.id',
            ['manifestId' => $manifestId],
        );

        return $manifest;
    }

    /** @return list<array<string, mixed>> */
    public function loadingManifests(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT m.id, m.code, m.tour_reference, m.vehicle_reference, m.status, m.created_at, '
            . 'COUNT(ms.shipment_id) shipment_count, '
            . "COALESCE(SUM(CASE WHEN ms.status = 'loaded' THEN 1 ELSE 0 END), 0) loaded_shipment_count "
            . 'FROM wms_loading_manifest m '
            . 'LEFT JOIN wms_loading_manifest_shipment ms ON ms.manifest_id = m.id '
            . 'WHERE m.tenant_id = :tenantId '
            . 'GROUP BY m.id, m.code, m.tour_reference, m.vehicle_reference, m.status, m.created_at '
            . 'ORDER BY m.created_at DESC, m.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function shipmentsAvailableForLoading(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT s.id, s.shipment_number, s.carrier, s.service, s.tracking_number '
            . "FROM wms_shipment s WHERE s.tenant_id = :tenantId AND s.status = 'labelled' "
            . 'AND NOT EXISTS (SELECT 1 FROM wms_loading_manifest_shipment ms WHERE ms.shipment_id = s.id) '
            . 'ORDER BY s.created_at, s.id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function outboxMessages(string $tenantId, string $status, int $limit, ?string $cursor): array
    {
        $messages = $this->connection->fetchAllAssociative(
            'SELECT id, event_name, aggregate_type, aggregate_id, payload, status, occurred_at, created_by, '
            . 'attempt_count, next_attempt_at, published_at, last_error, retried_by, retried_at '
            . 'FROM wms_integration_outbox WHERE tenant_id = :tenantId AND status = :status '
            . 'AND (:cursorFilter IS NULL OR id > :cursorValue) ORDER BY id LIMIT ' . $limit,
            ['tenantId' => $tenantId, 'status' => $status, 'cursorFilter' => $cursor, 'cursorValue' => $cursor ?? ''],
        );

        return array_map($this->decodeOutboxPayload(...), $messages);
    }

    /** @return array<string, mixed>|null */
    public function outboxMessage(string $tenantId, string $messageId): ?array
    {
        $message = $this->connection->fetchAssociative(
            'SELECT id, event_name, aggregate_type, aggregate_id, payload, status, occurred_at, '
            . 'created_by, acknowledged_by, acknowledged_at, attempt_count, next_attempt_at, claimed_at, '
            . 'published_at, last_error, retried_by, retried_at '
            . 'FROM wms_integration_outbox WHERE id = :messageId AND tenant_id = :tenantId',
            ['messageId' => $messageId, 'tenantId' => $tenantId],
        );

        return $message === false ? null : $this->decodeOutboxPayload($message);
    }

    /** @return list<array<string, mixed>> */
    public function carrierConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, carrier_code, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_carrier_connection WHERE tenant_id = :tenantId ORDER BY carrier_code, name, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function carrierConnection(string $tenantId, string $connectionId): ?array
    {
        $connection = $this->connection->fetchAssociative(
            'SELECT id, name, carrier_code, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_carrier_connection WHERE id = :id AND tenant_id = :tenantId',
            ['id' => $connectionId, 'tenantId' => $tenantId],
        );

        return $connection === false ? null : $connection;
    }

    /** @return list<array<string, mixed>> */
    public function printers(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_printer WHERE tenant_id = :tenantId ORDER BY name, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function printer(string $tenantId, string $printerId): ?array
    {
        $printer = $this->connection->fetchAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_printer WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $printerId],
        );

        return $printer === false ? null : $printer;
    }

    /** @return list<array<string, mixed>> */
    public function printJobs(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT j.id, j.printer_id, p.name printer_name, j.document_type, j.document_reference, '
            . 'j.format, j.copies, j.status, j.attempts, j.external_reference, j.last_error, '
            . 'j.created_by, j.created_at, j.completed_at FROM wms_print_job j '
            . 'INNER JOIN wms_printer p ON p.id = j.printer_id AND p.tenant_id = j.tenant_id '
            . 'WHERE j.tenant_id = :tenantId ORDER BY j.created_at DESC, j.id DESC LIMIT 100',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function printJob(string $tenantId, string $jobId): ?array
    {
        $job = $this->connection->fetchAssociative(
            'SELECT j.id, j.printer_id, p.name printer_name, j.document_type, j.document_reference, '
            . 'j.format, j.copies, j.status, j.attempts, j.external_reference, j.last_error, '
            . 'j.created_by, j.created_at, j.completed_at FROM wms_print_job j '
            . 'INNER JOIN wms_printer p ON p.id = j.printer_id AND p.tenant_id = j.tenant_id '
            . 'WHERE j.tenant_id = :tenantId AND j.id = :id',
            ['tenantId' => $tenantId, 'id' => $jobId],
        );

        return $job === false ? null : $job;
    }

    /** @return list<array<string, mixed>> */
    public function devices(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_device WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function device(string $tenantId, string $deviceId): ?array
    {
        $device = $this->connection->fetchAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_device WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );

        return $device === false ? null : $device;
    }

    /** @return list<array<string, mixed>> */
    public function scanEvents(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.device_id, d.code device_code, d.name device_name, e.scan_type, e.scan_value, '
            . 'e.process_type, e.context_reference, e.request_id, e.status, e.message, '
            . 'e.scanned_by, e.scanned_at FROM wms_scan_event e '
            . 'INNER JOIN wms_device d ON d.id = e.device_id AND d.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId ORDER BY e.scanned_at DESC, e.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function scanEvent(string $tenantId, string $eventId): ?array
    {
        $event = $this->connection->fetchAssociative(
            'SELECT e.id, e.device_id, d.code device_code, d.name device_name, e.scan_type, e.scan_value, '
            . 'e.process_type, e.context_reference, e.request_id, e.status, e.message, '
            . 'e.scanned_by, e.scanned_at FROM wms_scan_event e '
            . 'INNER JOIN wms_device d ON d.id = e.device_id AND d.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId AND e.id = :id',
            ['tenantId' => $tenantId, 'id' => $eventId],
        );

        return $event === false ? null : $event;
    }

    /** @return list<array<string, mixed>> */
    public function measurementDevices(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_measurement_device WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function measurementDevice(string $tenantId, string $deviceId): ?array
    {
        $device = $this->connection->fetchAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_measurement_device WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );

        return $device === false ? null : $device;
    }

    /** @return list<array<string, mixed>> */
    public function measurements(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT m.id, m.device_id, d.code device_code, d.name device_name, m.target_type, m.target_id, '
            . 'm.weight_grams, m.length_mm, m.width_mm, m.height_mm, m.request_id, m.status, m.message, '
            . 'm.measured_by, m.measured_at FROM wms_measurement m '
            . 'INNER JOIN wms_measurement_device d ON d.id = m.device_id AND d.tenant_id = m.tenant_id '
            . 'WHERE m.tenant_id = :tenantId ORDER BY m.measured_at DESC, m.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function measurement(string $tenantId, string $measurementId): ?array
    {
        $measurement = $this->connection->fetchAssociative(
            'SELECT m.id, m.device_id, d.code device_code, d.name device_name, m.target_type, m.target_id, '
            . 'm.weight_grams, m.length_mm, m.width_mm, m.height_mm, m.request_id, m.status, m.message, '
            . 'm.measured_by, m.measured_at FROM wms_measurement m '
            . 'INNER JOIN wms_measurement_device d ON d.id = m.device_id AND d.tenant_id = m.tenant_id '
            . 'WHERE m.tenant_id = :tenantId AND m.id = :id',
            ['tenantId' => $tenantId, 'id' => $measurementId],
        );

        return $measurement === false ? null : $measurement;
    }

    /** @return list<array<string, mixed>> */
    public function measurablePackages(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT p.id, p.package_number, o.code packing_order_code, p.weight_grams, '
            . 'p.length_mm, p.width_mm, p.height_mm FROM wms_package p '
            . 'INNER JOIN wms_packing_order o ON o.id = p.packing_order_id '
            . "WHERE o.tenant_id = :tenantId AND o.status IN ('open', 'packing') ORDER BY o.code, p.package_number",
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function automationDevices(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, device_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_automation_device WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function automationDevice(string $tenantId, string $deviceId): ?array
    {
        $device = $this->connection->fetchAssociative(
            'SELECT id, code, name, device_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_automation_device WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );

        return $device === false ? null : $device;
    }

    /** @return list<array<string, mixed>> */
    public function deviceCommands(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT c.id, c.device_id, d.code device_code, d.name device_name, c.command_type, c.location_id, '
            . 'l.code location_code, c.reference_type, c.reference_id, c.request_id, c.status, c.message, '
            . 'c.created_by, c.created_at, c.changed_by, c.changed_at FROM wms_device_command c '
            . 'INNER JOIN wms_automation_device d ON d.id = c.device_id AND d.tenant_id = c.tenant_id '
            . 'INNER JOIN wms_storage_location l ON l.id = c.location_id AND l.tenant_id = c.tenant_id '
            . 'WHERE c.tenant_id = :tenantId ORDER BY c.created_at DESC, c.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function deviceCommand(string $tenantId, string $commandId): ?array
    {
        $command = $this->connection->fetchAssociative(
            'SELECT c.id, c.device_id, d.code device_code, d.name device_name, c.command_type, c.location_id, '
            . 'l.code location_code, c.reference_type, c.reference_id, c.request_id, c.status, c.message, '
            . 'c.created_by, c.created_at, c.changed_by, c.changed_at FROM wms_device_command c '
            . 'INNER JOIN wms_automation_device d ON d.id = c.device_id AND d.tenant_id = c.tenant_id '
            . 'INNER JOIN wms_storage_location l ON l.id = c.location_id AND l.tenant_id = c.tenant_id '
            . 'WHERE c.tenant_id = :tenantId AND c.id = :id',
            ['tenantId' => $tenantId, 'id' => $commandId],
        );

        return $command === false ? null : $command;
    }

    /** @return list<array<string, mixed>> */
    public function automationLocations(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT l.id, l.code, w.code warehouse_code FROM wms_storage_location l '
            . 'INNER JOIN wms_warehouse w ON w.id = l.warehouse_id AND w.tenant_id = l.tenant_id '
            . 'WHERE l.tenant_id = :tenantId ORDER BY w.code, l.code',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function wcsConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, system_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_wcs_connection WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function wcsConnection(string $tenantId, string $connectionId): ?array
    {
        $connection = $this->connection->fetchAssociative(
            'SELECT id, code, name, system_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_wcs_connection WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $connectionId],
        );

        return $connection === false ? null : $connection;
    }

    /** @return list<array<string, mixed>> */
    public function machineCommands(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT c.id, c.connection_id, w.code connection_code, c.command_type, c.source, c.destination, '
            . 'c.load_unit, c.request_id, c.status, c.message, c.created_by, c.created_at, c.changed_by, c.changed_at '
            . 'FROM wms_machine_command c INNER JOIN wms_wcs_connection w ON w.id = c.connection_id '
            . 'AND w.tenant_id = c.tenant_id WHERE c.tenant_id = :tenantId ORDER BY c.created_at DESC, c.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function machineCommand(string $tenantId, string $commandId): ?array
    {
        $command = $this->connection->fetchAssociative(
            'SELECT c.id, c.connection_id, w.code connection_code, c.command_type, c.source, c.destination, '
            . 'c.load_unit, c.request_id, c.status, c.message, c.created_by, c.created_at, c.changed_by, c.changed_at '
            . 'FROM wms_machine_command c INNER JOIN wms_wcs_connection w ON w.id = c.connection_id '
            . 'AND w.tenant_id = c.tenant_id WHERE c.tenant_id = :tenantId AND c.id = :id',
            ['tenantId' => $tenantId, 'id' => $commandId],
        );

        return $command === false ? null : $command;
    }

    /** @return list<array<string, mixed>> */
    public function machineStatuses(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT s.id, s.connection_id, w.code connection_code, s.command_id, s.machine_code, s.status, '
            . 's.message, s.external_event_id, s.recorded_by, s.recorded_at FROM wms_machine_status s '
            . 'INNER JOIN wms_wcs_connection w ON w.id = s.connection_id AND w.tenant_id = s.tenant_id '
            . 'WHERE s.tenant_id = :tenantId ORDER BY s.recorded_at DESC, s.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    /**
     * @param array<string, mixed> $message
     *
     * @return array<string, mixed>
     */
    private function decodeOutboxPayload(array $message): array
    {
        if (!is_string($message['payload'] ?? null)) {
            throw new \LogicException('The outbox payload projection is invalid.');
        }
        $payload = json_decode($message['payload'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($payload)) {
            throw new \LogicException('The outbox payload must decode to an object.');
        }
        $message['payload'] = $payload;

        return $message;
    }
}
