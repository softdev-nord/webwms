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

    /** @return array{sites: list<array<string, mixed>>, warehouses: list<array<string, mixed>>, areas: list<array<string, mixed>>, aisles: list<array<string, mixed>>, bins: list<array<string, mixed>>} */
    public function warehouseTopology(string $tenantId): array
    {
        return [
            'sites' => $this->connection->fetchAllAssociative(
                'SELECT id, code, name, timezone, status, created_at FROM wms_site WHERE tenant_id = :tenantId ORDER BY code',
                ['tenantId' => $tenantId],
            ),
            'warehouses' => $this->connection->fetchAllAssociative(
                'SELECT w.id, w.site_id, w.code, w.name, w.warehouse_type, s.code site_code, w.created_at '
                . 'FROM wms_warehouse w INNER JOIN wms_site s ON s.id = w.site_id AND s.tenant_id = w.tenant_id '
                . 'WHERE w.tenant_id = :tenantId ORDER BY s.code, w.code',
                ['tenantId' => $tenantId],
            ),
            'areas' => $this->connection->fetchAllAssociative(
                'SELECT id, warehouse_id, code, name, area_type, created_at FROM wms_warehouse_area '
                . 'WHERE tenant_id = :tenantId ORDER BY warehouse_id, code',
                ['tenantId' => $tenantId],
            ),
            'aisles' => $this->connection->fetchAllAssociative(
                'SELECT id, area_id, code, name, created_at FROM wms_warehouse_aisle '
                . 'WHERE tenant_id = :tenantId ORDER BY area_id, code',
                ['tenantId' => $tenantId],
            ),
            'bins' => $this->connection->fetchAllAssociative(
                'SELECT id, warehouse_id, area_id, aisle_id, code, level_code, bin_code, location_type, '
                . 'capacity_quantity, putaway_enabled, putaway_priority, created_at FROM wms_storage_location '
                . 'WHERE tenant_id = :tenantId ORDER BY warehouse_id, area_id, aisle_id, level_code, bin_code, code',
                ['tenantId' => $tenantId],
            ),
        ];
    }

    /** @return array{warehouses: list<array<string, mixed>>, areas: list<array<string, mixed>>} */
    public function warehouseOverview(string $tenantId): array
    {
        return [
            'warehouses' => $this->connection->fetchAllAssociative(
                'SELECT w.id, w.code, w.name, w.warehouse_type, '
                . '(SELECT COUNT(*) FROM wms_storage_location l WHERE l.warehouse_id = w.id) bin_count, '
                . '(SELECT COUNT(*) FROM wms_storage_location l WHERE l.warehouse_id = w.id AND EXISTS (SELECT 1 FROM wms_stock_balance b WHERE b.location_id = l.id AND b.quantity > 0)) occupied_bin_count, '
                . '(SELECT COALESCE(SUM(l.capacity_quantity), 0) FROM wms_storage_location l WHERE l.warehouse_id = w.id) total_capacity, '
                . '(SELECT COALESCE(SUM(b.quantity), 0) FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id WHERE l.warehouse_id = w.id) stock_quantity '
                . 'FROM wms_warehouse w WHERE w.tenant_id = :tenantId ORDER BY w.code',
                ['tenantId' => $tenantId],
            ),
            'areas' => $this->connection->fetchAllAssociative(
                'SELECT a.id, a.warehouse_id, a.code, a.name, a.area_type, '
                . '(SELECT COUNT(*) FROM wms_storage_location l WHERE l.area_id = a.id) bin_count, '
                . '(SELECT COUNT(*) FROM wms_storage_location l WHERE l.area_id = a.id AND EXISTS (SELECT 1 FROM wms_stock_balance b WHERE b.location_id = l.id AND b.quantity > 0)) occupied_bin_count, '
                . '(SELECT COALESCE(SUM(l.capacity_quantity), 0) FROM wms_storage_location l WHERE l.area_id = a.id) total_capacity, '
                . '(SELECT COALESCE(SUM(b.quantity), 0) FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id WHERE l.area_id = a.id) stock_quantity '
                . 'FROM wms_warehouse_area a WHERE a.tenant_id = :tenantId ORDER BY a.warehouse_id, a.code',
                ['tenantId' => $tenantId],
            ),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function warehouseOccupancy(string $tenantId, ?string $warehouseId = null): array
    {
        $warehouseFilter = $warehouseId === null ? '' : 'AND l.warehouse_id = :warehouseId ';
        $parameters = ['tenantId' => $tenantId];
        if ($warehouseId !== null) {
            $parameters['warehouseId'] = $warehouseId;
        }

        return $this->connection->fetchAllAssociative(
            'SELECT l.id, l.warehouse_id, w.code warehouse_code, w.name warehouse_name, '
            . 'l.area_id, a.code area_code, a.name area_name, a.area_type, '
            . 'l.aisle_id, g.code aisle_code, g.name aisle_name, l.code location_code, '
            . 'l.level_code, l.bin_code, l.location_type, l.capacity_quantity, '
            . 'COALESCE(SUM(CASE WHEN b.quantity > 0 THEN b.quantity ELSE 0 END), 0) stock_quantity, '
            . 'COUNT(DISTINCT CASE WHEN b.quantity > 0 THEN b.product_id END) product_count, '
            . 'COUNT(DISTINCT CASE WHEN b.quantity > 0 THEN b.stock_key END) stock_position_count, '
            . "COALESCE(SUM(CASE WHEN b.quantity > 0 AND b.stock_status = 'available' THEN b.quantity ELSE 0 END), 0) available_quantity, "
            . "COALESCE(SUM(CASE WHEN b.quantity > 0 AND b.stock_status = 'quality_inspection' THEN b.quantity ELSE 0 END), 0) quality_quantity, "
            . "COALESCE(SUM(CASE WHEN b.quantity > 0 AND b.stock_status = 'blocked' THEN b.quantity ELSE 0 END), 0) blocked_quantity, "
            . "GROUP_CONCAT(DISTINCT CASE WHEN b.quantity > 0 THEN p.sku END ORDER BY p.sku SEPARATOR ', ') product_skus "
            . 'FROM wms_storage_location l INNER JOIN wms_warehouse w ON w.id = l.warehouse_id AND w.tenant_id = l.tenant_id '
            . 'LEFT JOIN wms_warehouse_area a ON a.id = l.area_id AND a.tenant_id = l.tenant_id '
            . 'LEFT JOIN wms_warehouse_aisle g ON g.id = l.aisle_id AND g.tenant_id = l.tenant_id '
            . 'LEFT JOIN wms_stock_balance b ON b.location_id = l.id AND b.tenant_id = l.tenant_id '
            . 'LEFT JOIN wms_product_reference p ON p.id = b.product_id AND p.tenant_id = b.tenant_id '
            . 'WHERE l.tenant_id = :tenantId ' . $warehouseFilter
            . 'GROUP BY l.id, l.warehouse_id, w.code, w.name, l.area_id, a.code, a.name, a.area_type, '
            . 'l.aisle_id, g.code, g.name, l.code, l.level_code, l.bin_code, l.location_type, l.capacity_quantity '
            . 'ORDER BY w.code, a.code, g.code, l.level_code DESC, l.bin_code, l.code',
            $parameters,
        );
    }

    /** @return list<array<string, mixed>> */
    public function receivingLocations(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT l.id, l.code, w.code warehouse_code FROM wms_storage_location l '
            . 'INNER JOIN wms_warehouse w ON w.id = l.warehouse_id AND w.tenant_id = l.tenant_id '
            . 'WHERE l.tenant_id = :tenantId ORDER BY w.code, l.code',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function unplannedReceipts(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT r.id, r.code, r.delivery_note, r.status, r.accepted_at, r.booked_at, s.code supplier_code, '
            . 's.name supplier_name, COUNT(i.id) item_count, COALESCE(SUM(i.quantity), 0) total_quantity '
            . 'FROM wms_unplanned_receipt r INNER JOIN wms_supplier s ON s.id = r.supplier_id '
            . 'LEFT JOIN wms_unplanned_receipt_item i ON i.receipt_id = r.id WHERE r.tenant_id = :tenantId '
            . 'GROUP BY r.id, r.code, r.delivery_note, r.status, r.accepted_at, r.booked_at, s.code, s.name '
            . 'ORDER BY r.accepted_at DESC, r.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function unplannedReceipt(string $tenantId, string $receiptId): ?array
    {
        $receipt = $this->connection->fetchAssociative(
            'SELECT r.id, r.code, r.delivery_note, r.status, r.accepted_at, r.booked_at, s.code supplier_code, '
            . 's.name supplier_name FROM wms_unplanned_receipt r INNER JOIN wms_supplier s ON s.id = r.supplier_id '
            . 'WHERE r.tenant_id = :tenantId AND r.id = :id',
            ['tenantId' => $tenantId, 'id' => $receiptId],
        );
        if ($receipt === false) {
            return null;
        }
        $receipt['items'] = $this->connection->fetchAllAssociative(
            'SELECT i.id, i.product_id, p.sku, p.name product_name, i.location_id, l.code location_code, '
            . 'i.quantity, i.stock_status, i.batch_number, i.serial_number, i.expires_at '
            . 'FROM wms_unplanned_receipt_item i INNER JOIN wms_product_reference p ON p.id = i.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = i.location_id WHERE i.receipt_id = :receiptId ORDER BY i.id',
            ['receiptId' => $receiptId],
        );

        return $receipt;
    }

    /** @return list<array<string, mixed>> */
    public function plannedInboundWorklist(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT d.id delivery_id, d.code delivery_code, d.delivery_note, d.expected_at, d.status delivery_status, '
            . 'o.code order_number, l.id line_id, l.advised_quantity, l.status line_status, p.sku, p.name product_name, '
            . 'r.id receipt_id, r.quantity receipt_quantity, r.status receipt_status, r.quality_decision, '
            . 'r.stock_status, r.location_id, source.code source_location_code, r.received_at, r.inspected_at, '
            . 'x.discrepancy_type, x.expected_quantity, x.actual_quantity, x.reason discrepancy_reason, '
            . 'x.status discrepancy_status, x.resolution_note, x.resolved_at, '
            . 'po.id putaway_order_id, po.status putaway_status, po.quantity putaway_quantity, '
            . 'target.code target_location_code, po.created_at putaway_created_at, po.confirmed_at putaway_confirmed_at '
            . 'FROM wms_inbound_delivery d INNER JOIN wms_purchase_order o ON o.id = d.purchase_order_id '
            . 'INNER JOIN wms_inbound_delivery_line l ON l.inbound_delivery_id = d.id '
            . 'INNER JOIN wms_purchase_order_item i ON i.id = l.purchase_order_item_id '
            . 'INNER JOIN wms_product_reference p ON p.id = i.product_id '
            . 'LEFT JOIN wms_inbound_receipt r ON r.inbound_delivery_line_id = l.id '
            . 'LEFT JOIN wms_storage_location source ON source.id = r.location_id '
            . 'LEFT JOIN wms_inbound_discrepancy x ON x.receipt_id = r.id '
            . 'LEFT JOIN wms_putaway_order po ON po.inbound_receipt_id = r.id '
            . 'LEFT JOIN wms_storage_location target ON target.id = po.target_location_id '
            . 'WHERE d.tenant_id = :tenantId ORDER BY d.expected_at, d.code, l.id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function inboundControlCenter(string $tenantId): array
    {
        return [
            'purchaseOrders' => $this->connection->fetchAllAssociative(
                'SELECT o.id, o.code, o.supplier_reference, o.status, o.created_at, COUNT(i.id) item_count, COALESCE(SUM(i.ordered_quantity), 0) ordered_quantity, COALESCE(SUM(i.received_quantity), 0) received_quantity FROM wms_purchase_order o LEFT JOIN wms_purchase_order_item i ON i.purchase_order_id = o.id WHERE o.tenant_id = :tenantId GROUP BY o.id, o.code, o.supplier_reference, o.status, o.created_at ORDER BY o.created_at DESC',
                ['tenantId' => $tenantId],
            ),
            'purchaseOrderItems' => $this->connection->fetchAllAssociative(
                'SELECT i.id, i.purchase_order_id, o.code order_code, i.product_id, p.sku, p.name product_name, i.ordered_quantity, i.advised_quantity, i.received_quantity FROM wms_purchase_order_item i INNER JOIN wms_purchase_order o ON o.id = i.purchase_order_id INNER JOIN wms_product_reference p ON p.id = i.product_id WHERE o.tenant_id = :tenantId AND i.advised_quantity < i.ordered_quantity ORDER BY o.created_at DESC, i.id',
                ['tenantId' => $tenantId],
            ),
            'returns' => $this->connection->fetchAllAssociative(
                'SELECT o.id order_id, o.code, o.order_reference, o.status order_status, i.id item_id, p.sku, p.name product_name, i.expected_quantity, i.reason, i.status item_status, r.id receipt_id, r.status receipt_status, r.quality_decision, r.inspection_note FROM wms_return_order o INNER JOIN wms_return_item i ON i.return_order_id = o.id INNER JOIN wms_product_reference p ON p.id = i.product_id LEFT JOIN wms_return_receipt r ON r.return_item_id = i.id WHERE o.tenant_id = :tenantId ORDER BY o.created_at DESC, i.id',
                ['tenantId' => $tenantId],
            ),
            'checklists' => $this->connection->fetchAllAssociative('SELECT id, code, name, questions, JSON_LENGTH(questions) question_count, active, created_at FROM wms_quality_checklist WHERE tenant_id = :tenantId ORDER BY code', ['tenantId' => $tenantId]),
            'attachments' => $this->connection->fetchAllAssociative('SELECT id, aggregate_type, aggregate_id, category, original_name, media_type, byte_size, checksum, created_at FROM wms_inbound_attachment WHERE tenant_id = :tenantId ORDER BY created_at DESC', ['tenantId' => $tenantId]),
            'labels' => $this->connection->fetchAllAssociative('SELECT id, aggregate_type, aggregate_id, label_type, copies, status, created_at, printed_at FROM wms_inbound_label_job WHERE tenant_id = :tenantId ORDER BY created_at DESC', ['tenantId' => $tenantId]),
            'crossDock' => $this->connection->fetchAllAssociative('SELECT a.id, a.inbound_receipt_id, a.outbound_order_item_id, a.quantity, a.status, a.created_at, p.sku, o.order_number outbound_order_code FROM wms_cross_dock_assignment a INNER JOIN wms_outbound_order_item i ON i.id = a.outbound_order_item_id INNER JOIN wms_outbound_order o ON o.id = i.outbound_order_id INNER JOIN wms_product_reference p ON p.id = i.product_id WHERE a.tenant_id = :tenantId ORDER BY a.created_at DESC', ['tenantId' => $tenantId]),
            'outboundDemand' => $this->connection->fetchAllAssociative("SELECT i.id, o.order_number order_code, p.sku, p.name product_name, i.requested_quantity FROM wms_outbound_order_item i INNER JOIN wms_outbound_order o ON o.id = i.outbound_order_id INNER JOIN wms_product_reference p ON p.id = i.product_id WHERE o.tenant_id = :tenantId AND o.status IN ('imported', 'released') ORDER BY o.created_at DESC", ['tenantId' => $tenantId]),
            'productionReceipts' => $this->connection->fetchAllAssociative('SELECT r.id, r.production_order, p.sku, p.name product_name, l.code location_code, r.quantity, r.batch_number, r.status, r.received_at FROM wms_production_receipt r INNER JOIN wms_product_reference p ON p.id = r.product_id INNER JOIN wms_storage_location l ON l.id = r.location_id WHERE r.tenant_id = :tenantId ORDER BY r.received_at DESC', ['tenantId' => $tenantId]),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function stock(string $tenantId, ?string $warehouseId, int $limit, ?string $cursor): array
    {
        return $this->connection->fetchAllAssociative(
            "SELECT CONCAT(b.product_id, '|', b.location_id, '|', b.stock_key) AS `cursor`, "
            . 'b.product_id, p.sku, p.name product_name, b.location_id, b.stock_key, l.code location_code, l.warehouse_id, '
            . 'w.code warehouse_code, a.code area_code, ai.code aisle_code, l.level_code, l.bin_code, '
            . 'b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity, b.updated_at, '
            . 't.id special_stock_type_id, t.code special_stock_code, t.name special_stock_name, t.allocatable, c.owner_reference, c.reason classification_reason, '
            . "b.quantity - COALESCE((SELECT SUM(a.quantity) FROM wms_stock_allocation a WHERE a.tenant_id = b.tenant_id AND a.product_id = b.product_id AND a.location_id = b.location_id AND a.stock_key = b.stock_key AND a.status = 'active'), 0) available_quantity "
            . 'FROM wms_stock_balance b INNER JOIN wms_product_reference p ON p.id = b.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = b.location_id '
            . 'INNER JOIN wms_warehouse w ON w.id = l.warehouse_id AND w.tenant_id = b.tenant_id '
            . 'LEFT JOIN wms_warehouse_area a ON a.id = l.area_id LEFT JOIN wms_warehouse_aisle ai ON ai.id = l.aisle_id '
            . 'LEFT JOIN wms_stock_classification c ON c.tenant_id = b.tenant_id AND c.product_id = b.product_id AND c.location_id = b.location_id AND c.stock_key = b.stock_key '
            . 'LEFT JOIN wms_special_stock_type t ON t.id = c.special_stock_type_id '
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
            . 'e.allocation_id, e.reservation_id, e.reason, e.performed_by, u.display_name performed_by_name, e.occurred_at '
            . 'FROM wms_stock_ledger e INNER JOIN wms_product_reference p ON p.id = e.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = e.location_id '
            . 'INNER JOIN wms_user_account u ON u.id = e.performed_by AND u.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId '
            . 'AND (:productFilter IS NULL OR e.product_id = :productId) '
            . 'AND (:locationFilter IS NULL OR e.location_id = :locationId) '
            . 'AND (:transferFilter IS NULL OR e.transfer_id = :transferId) '
            . 'AND (:movementTypeFilter IS NULL OR e.movement_type = :movementType) '
            . 'AND (:cursorFilter IS NULL OR e.id < :cursorValue) '
            . 'ORDER BY e.occurred_at DESC, e.id DESC LIMIT ' . $limit,
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
    public function specialStockTypes(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, classification_kind, allocatable, active, created_at, changed_at '
            . 'FROM wms_special_stock_type WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stockSelectionRules(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT r.id, r.code, r.name, r.strategy, r.priority, r.enabled, r.warehouse_id, w.code warehouse_code, '
            . 'r.product_id, p.sku product_sku, r.created_at FROM wms_stock_selection_rule r '
            . 'LEFT JOIN wms_warehouse w ON w.id = r.warehouse_id AND w.tenant_id = r.tenant_id '
            . 'LEFT JOIN wms_product_reference p ON p.id = r.product_id AND p.tenant_id = r.tenant_id '
            . 'WHERE r.tenant_id = :tenantId ORDER BY r.priority, r.code',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stockSelectionEvents(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.reservation_id, e.requested_quantity, e.allocated_quantity, e.candidate_count, '
            . 'e.occurred_at, r.code rule_code, r.strategy, p.sku, u.display_name performed_by_name '
            . 'FROM wms_stock_selection_event e INNER JOIN wms_stock_selection_rule r ON r.id = e.rule_id AND r.tenant_id = e.tenant_id '
            . 'INNER JOIN wms_product_reference p ON p.id = e.product_id AND p.tenant_id = e.tenant_id '
            . 'INNER JOIN wms_user_account u ON u.id = e.performed_by AND u.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId ORDER BY e.occurred_at DESC, e.id DESC LIMIT ' . max(1, min($limit, 500)),
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stockBlockReasons(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, description, active, created_at FROM wms_stock_block_reason '
            . 'WHERE tenant_id = :tenantId ORDER BY code',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stockBlocks(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT b.id, b.product_id, p.sku, p.name product_name, b.location_id, l.code location_code, '
            . 'b.original_status, b.batch_number, b.serial_number, b.expires_at, b.quantity, b.note, b.status, '
            . 'r.code reason_code, r.name reason_name, blocker.display_name blocked_by_name, b.blocked_at, '
            . 'reviewer.display_name reviewed_by_name, b.reviewed_at, b.review_note, releaser.display_name released_by_name, b.released_at '
            . 'FROM wms_stock_block b INNER JOIN wms_stock_block_reason r ON r.id = b.reason_id AND r.tenant_id = b.tenant_id '
            . 'INNER JOIN wms_product_reference p ON p.id = b.product_id AND p.tenant_id = b.tenant_id '
            . 'INNER JOIN wms_storage_location l ON l.id = b.location_id AND l.tenant_id = b.tenant_id '
            . 'INNER JOIN wms_user_account blocker ON blocker.id = b.blocked_by AND blocker.tenant_id = b.tenant_id '
            . 'LEFT JOIN wms_user_account reviewer ON reviewer.id = b.reviewed_by AND reviewer.tenant_id = b.tenant_id '
            . 'LEFT JOIN wms_user_account releaser ON releaser.id = b.released_by AND releaser.tenant_id = b.tenant_id '
            . 'WHERE b.tenant_id = :tenantId ORDER BY CASE b.status WHEN \'open\' THEN 0 WHEN \'reviewed\' THEN 1 ELSE 2 END, b.blocked_at DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function stockBlockEvents(string $tenantId, string $blockId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.event_type, e.note, u.display_name performed_by_name, e.occurred_at '
            . 'FROM wms_stock_block_event e INNER JOIN wms_user_account u ON u.id = e.performed_by AND u.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId AND e.block_id = :blockId ORDER BY e.occurred_at, e.id',
            ['tenantId' => $tenantId, 'blockId' => $blockId],
        );
    }

    /** @return array{batches: list<array<string, mixed>>, expiries: list<array<string, mixed>>, serials: list<array<string, mixed>>} */
    public function traceability(string $tenantId, int $expiryWarningDays = 30): array
    {
        $expiryWarningDays = max(0, min($expiryWarningDays, 365));
        $batches = $this->connection->fetchAllAssociative(
            'SELECT b.product_id, p.sku, p.name product_name, b.batch_number, MIN(b.expires_at) earliest_expiry, '
            . 'SUM(b.quantity) quantity, COUNT(DISTINCT b.location_id) location_count '
            . 'FROM wms_stock_balance b INNER JOIN wms_product_reference p ON p.id = b.product_id '
            . 'WHERE b.tenant_id = :tenantId AND b.batch_number IS NOT NULL '
            . 'GROUP BY b.product_id, p.sku, p.name, b.batch_number ORDER BY p.sku, b.batch_number',
            ['tenantId' => $tenantId],
        );
        $expiries = $this->connection->fetchAllAssociative(
            "SELECT b.product_id, p.sku, p.name product_name, b.batch_number, b.expires_at, SUM(b.quantity) quantity, "
            . "CASE WHEN b.expires_at < CURRENT_DATE THEN 'expired' WHEN b.expires_at <= DATE_ADD(CURRENT_DATE, INTERVAL " . $expiryWarningDays . " DAY) THEN 'critical' ELSE 'ok' END expiry_status "
            . 'FROM wms_stock_balance b INNER JOIN wms_product_reference p ON p.id = b.product_id '
            . 'WHERE b.tenant_id = :tenantId AND b.expires_at IS NOT NULL '
            . 'GROUP BY b.product_id, p.sku, p.name, b.batch_number, b.expires_at '
            . 'ORDER BY b.expires_at, p.sku, b.batch_number',
            ['tenantId' => $tenantId],
        );
        $serials = $this->connection->fetchAllAssociative(
            'SELECT b.product_id, p.sku, p.name product_name, b.serial_number, b.stock_status, '
            . 'b.location_id, l.code location_code, b.quantity, b.updated_at '
            . 'FROM wms_stock_balance b INNER JOIN wms_product_reference p ON p.id = b.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = b.location_id '
            . 'WHERE b.tenant_id = :tenantId AND b.serial_number IS NOT NULL '
            . 'ORDER BY p.sku, b.serial_number',
            ['tenantId' => $tenantId],
        );

        return ['batches' => $batches, 'expiries' => $expiries, 'serials' => $serials];
    }

    /** @return list<array<string, mixed>> */
    public function traceabilityEvents(string $tenantId, string $dimension, string $value): array
    {
        $column = match ($dimension) {
            'batch' => 'e.batch_number',
            'serial' => 'e.serial_number',
            default => throw new \InvalidArgumentException('The traceability dimension is invalid.'),
        };

        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.product_id, p.sku, p.name product_name, e.location_id, l.code location_code, '
            . 'e.stock_status, e.batch_number, e.serial_number, e.expires_at, e.quantity_delta, '
            . 'e.resulting_quantity, e.movement_type, e.reason, u.display_name performed_by_name, e.occurred_at '
            . 'FROM wms_stock_ledger e INNER JOIN wms_product_reference p ON p.id = e.product_id '
            . 'INNER JOIN wms_storage_location l ON l.id = e.location_id '
            . 'INNER JOIN wms_user_account u ON u.id = e.performed_by AND u.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId AND ' . $column . ' = :value ORDER BY e.occurred_at, e.id',
            ['tenantId' => $tenantId, 'value' => $value],
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
            'SELECT o.id, o.order_number, o.customer_reference, o.status, o.created_at, o.released_at, o.cancelled_at, o.cancellation_reason, '
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
            'SELECT o.id, o.order_number, o.customer_reference, o.status, o.created_at, o.released_at, o.cancelled_at, o.cancellation_reason, '
            . 'COUNT(i.id) item_count, COALESCE(SUM(i.requested_quantity), 0) requested_quantity, '
            . 'l.id pick_list_id, l.code pick_list_code, l.status pick_list_status '
            . 'FROM wms_outbound_order o LEFT JOIN wms_outbound_order_item i ON i.outbound_order_id = o.id '
            . 'LEFT JOIN wms_pick_list l ON l.outbound_order_id = o.id '
            . 'WHERE o.tenant_id = :tenantId '
            . 'GROUP BY o.id, o.order_number, o.customer_reference, o.status, o.created_at, o.released_at, o.cancelled_at, o.cancellation_reason, '
            . 'l.id, l.code, l.status ORDER BY o.created_at DESC, o.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function outboundControlCenter(string $tenantId): array
    {
        return [
            'forecast' => $this->connection->fetchAllAssociative(
                "SELECT i.product_id, p.sku, p.name product_name, SUM(i.requested_quantity) demand_quantity, COALESCE((SELECT SUM(b.quantity) FROM wms_stock_balance b WHERE b.tenant_id = o.tenant_id AND b.product_id = i.product_id AND b.stock_status = 'available'), 0) stock_quantity, GREATEST(SUM(i.requested_quantity) - COALESCE((SELECT SUM(b.quantity) FROM wms_stock_balance b WHERE b.tenant_id = o.tenant_id AND b.product_id = i.product_id AND b.stock_status = 'available'), 0), 0) shortage_quantity FROM wms_outbound_order_item i INNER JOIN wms_outbound_order o ON o.id = i.outbound_order_id INNER JOIN wms_product_reference p ON p.id = i.product_id WHERE o.tenant_id = :tenantId AND o.status IN ('imported', 'released') GROUP BY i.product_id, p.sku, p.name, o.tenant_id ORDER BY shortage_quantity DESC, p.sku",
                ['tenantId' => $tenantId],
            ),
            'qualityChecks' => $this->connection->fetchAllAssociative('SELECT q.id, q.pick_list_id, l.code pick_list_code, o.order_number, q.completeness_passed, q.condition_passed, q.customer_check_passed, q.note, q.decision, q.checked_at FROM wms_outbound_quality_check q INNER JOIN wms_pick_list l ON l.id = q.pick_list_id INNER JOIN wms_outbound_order o ON o.id = l.outbound_order_id WHERE q.tenant_id = :tenantId ORDER BY q.checked_at DESC', ['tenantId' => $tenantId]),
            'qualityCandidates' => $this->connection->fetchAllAssociative("SELECT l.id, l.code, o.order_number FROM wms_pick_list l INNER JOIN wms_outbound_order o ON o.id = l.outbound_order_id WHERE l.tenant_id = :tenantId AND l.status = 'completed' AND NOT EXISTS (SELECT 1 FROM wms_outbound_quality_check q WHERE q.pick_list_id = l.id AND q.decision = 'released') ORDER BY l.updated_at DESC", ['tenantId' => $tenantId]),
            'shippingRules' => $this->connection->fetchAllAssociative('SELECT id, code, name, carrier, service, min_weight_grams, max_weight_grams, priority, active FROM wms_shipping_rule WHERE tenant_id = :tenantId ORDER BY priority, code', ['tenantId' => $tenantId]),
            'trackingEvents' => $this->connection->fetchAllAssociative('SELECT e.id, e.shipment_id, s.shipment_number, e.status, e.location, e.description, e.source, e.occurred_at FROM wms_tracking_event e INNER JOIN wms_shipment s ON s.id = e.shipment_id WHERE e.tenant_id = :tenantId ORDER BY e.occurred_at DESC, e.id DESC', ['tenantId' => $tenantId]),
            'documents' => $this->connection->fetchAllAssociative('SELECT id, aggregate_type, aggregate_id, document_type, document_number, content_type, checksum, created_at FROM wms_shipping_document WHERE tenant_id = :tenantId ORDER BY created_at DESC', ['tenantId' => $tenantId]),
            'tours' => $this->connection->fetchAllAssociative('SELECT t.id, t.code, t.carrier, t.vehicle_reference, t.max_weight_grams, t.departure_at, t.status, COUNT(s.id) stop_count, COALESCE(SUM(p.weight_grams), 0) planned_weight_grams FROM wms_transport_tour t LEFT JOIN wms_tour_stop s ON s.tour_id = t.id LEFT JOIN wms_shipment sh ON sh.id = s.shipment_id LEFT JOIN wms_package p ON p.packing_order_id = sh.packing_order_id WHERE t.tenant_id = :tenantId GROUP BY t.id, t.code, t.carrier, t.vehicle_reference, t.max_weight_grams, t.departure_at, t.status ORDER BY t.departure_at, t.code', ['tenantId' => $tenantId]),
            'weightConstraints' => $this->connection->fetchAllAssociative('SELECT id, scope, reference_code, max_weight_grams, active, created_at FROM wms_weight_constraint WHERE tenant_id = :tenantId ORDER BY scope, reference_code', ['tenantId' => $tenantId]),
        ];
    }

    /** @return array<string, mixed>|null */
    public function shippingDocument(string $tenantId, string $documentId): ?array
    {
        $document = $this->connection->fetchAssociative('SELECT id, document_type, document_number, content, content_type FROM wms_shipping_document WHERE id = :id AND tenant_id = :tenantId', ['id' => $documentId, 'tenantId' => $tenantId]);

        return $document === false ? null : $document;
    }

    /** @return list<array<string, mixed>> */
    public function availableStockForProduct(string $tenantId, string $productId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT b.location_id, b.stock_key, l.code location_code, b.stock_status, b.batch_number, b.serial_number, '
            . 'b.expires_at, b.quantity - COALESCE(SUM(a.quantity), 0) available_quantity '
            . 'FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id '
            . "LEFT JOIN wms_stock_allocation a ON a.tenant_id = b.tenant_id AND a.product_id = b.product_id AND a.location_id = b.location_id AND a.stock_key = b.stock_key AND a.status = 'active' "
            . 'LEFT JOIN wms_stock_classification c ON c.tenant_id = b.tenant_id AND c.product_id = b.product_id AND c.location_id = b.location_id AND c.stock_key = b.stock_key '
            . 'LEFT JOIN wms_special_stock_type t ON t.id = c.special_stock_type_id '
            . "WHERE b.tenant_id = :tenantId AND b.product_id = :productId AND b.stock_status = 'available' AND (t.id IS NULL OR t.allocatable = 1) "
            . 'AND (b.expires_at IS NULL OR b.expires_at >= CURRENT_DATE) '
            . 'GROUP BY b.location_id, b.stock_key, l.code, b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity '
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

    public function outboundQualityDecision(string $tenantId, string $pickListId): ?string
    {
        $decision = $this->connection->fetchOne('SELECT decision FROM wms_outbound_quality_check WHERE tenant_id = :tenantId AND pick_list_id = :pickListId ORDER BY checked_at DESC, id DESC LIMIT 1', ['tenantId' => $tenantId, 'pickListId' => $pickListId]);

        return is_string($decision) ? $decision : null;
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

    /** @return list<array<string, mixed>> */
    public function transportEndpoints(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.code, e.name, e.adapter_type, e.address, e.credential_env, e.active, '
            . 'p.protocol, p.framing, p.connect_timeout_ms, p.read_timeout_ms, e.created_by, e.created_at, '
            . 'e.changed_by, e.changed_at FROM wms_transport_endpoint e INNER JOIN wms_protocol_configuration p '
            . 'ON p.endpoint_id = e.id WHERE e.tenant_id = :tenantId ORDER BY e.code, e.id',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed>|null */
    public function transportEndpoint(string $tenantId, string $endpointId): ?array
    {
        $endpoint = $this->connection->fetchAssociative(
            'SELECT e.id, e.code, e.name, e.adapter_type, e.address, e.credential_env, e.active, '
            . 'p.protocol, p.framing, p.connect_timeout_ms, p.read_timeout_ms, e.created_by, e.created_at, '
            . 'e.changed_by, e.changed_at FROM wms_transport_endpoint e INNER JOIN wms_protocol_configuration p '
            . 'ON p.endpoint_id = e.id WHERE e.tenant_id = :tenantId AND e.id = :id',
            ['tenantId' => $tenantId, 'id' => $endpointId],
        );

        return $endpoint === false ? null : $endpoint;
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
