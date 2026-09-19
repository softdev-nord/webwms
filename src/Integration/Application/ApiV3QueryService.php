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
            'SELECT id, sku, name, created_at FROM wms_product_reference '
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
            'SELECT id, order_number, customer_reference, status, created_at, released_at '
            . 'FROM wms_outbound_order WHERE id = :orderId AND tenant_id = :tenantId',
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
            . 'l.assigned_by, l.assigned_at, l.created_by, l.created_at, l.updated_at '
            . 'FROM wms_pick_list l LEFT JOIN wms_outbound_order o ON o.id = l.outbound_order_id '
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
            . 'o.created_at, o.updated_at, o.completed_by, o.completed_at '
            . 'FROM wms_packing_order o INNER JOIN wms_pick_list l ON l.id = o.pick_list_id '
            . 'WHERE o.id = :packingOrderId AND o.tenant_id = :tenantId',
            ['packingOrderId' => $packingOrderId, 'tenantId' => $tenantId],
        );
        if ($order === false) {
            return null;
        }
        $packages = $this->connection->fetchAllAssociative(
            'SELECT id, package_number, weight_grams, status, packed_by, packed_at '
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
            'SELECT id, printer_id, document_type, document_reference, format, copies, status, attempts, '
            . 'external_reference, last_error, created_by, created_at, completed_at FROM wms_print_job '
            . 'WHERE tenant_id = :tenantId ORDER BY created_at DESC, id DESC LIMIT 100',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function printJob(string $tenantId, string $jobId): ?array
    {
        $job = $this->connection->fetchAssociative(
            'SELECT id, printer_id, document_type, document_reference, format, copies, status, attempts, '
            . 'external_reference, last_error, created_by, created_at, completed_at FROM wms_print_job '
            . 'WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $jobId],
        );

        return $job === false ? null : $job;
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
