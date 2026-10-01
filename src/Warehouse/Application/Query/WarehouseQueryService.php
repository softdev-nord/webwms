<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Application\Query;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use WebWMS\Integration\Application\StockMovementCriteria;

readonly class WarehouseQueryService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function products(string $tenantId, int $limit, ?string $cursor): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, sku, name, weight_grams, length_mm, width_mm, height_mm, created_at FROM wms_product_reference '
            . 'WHERE tenant_id = :tenantId AND (:cursorFilter IS NULL OR id > :cursorValue) '
            . 'ORDER BY id ASC LIMIT ' . $limit,
            ['tenantId' => $tenantId, 'cursorFilter' => $cursor, 'cursorValue' => $cursor ?? ''],
        );
    }

    public function warehouses(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT w.id, w.site_id, w.code, w.name, COUNT(l.id) location_count '
            . 'FROM wms_warehouse w LEFT JOIN wms_storage_location l ON l.warehouse_id = w.id '
            . 'WHERE w.tenant_id = :tenantId GROUP BY w.id, w.site_id, w.code, w.name ORDER BY w.code',
            ['tenantId' => $tenantId],
        );
    }

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

    public function specialStockTypes(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, classification_kind, allocatable, active, created_at, changed_at '
            . 'FROM wms_special_stock_type WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

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

    public function stockBlockReasons(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, description, active, created_at FROM wms_stock_block_reason '
            . 'WHERE tenant_id = :tenantId ORDER BY code',
            ['tenantId' => $tenantId],
        );
    }

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
            . "WHERE b.tenant_id = :tenantId ORDER BY CASE b.status WHEN 'open' THEN 0 WHEN 'reviewed' THEN 1 ELSE 2 END, b.blocked_at DESC",
            ['tenantId' => $tenantId],
        );
    }

    public function stockBlockEvents(string $tenantId, string $blockId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.event_type, e.note, u.display_name performed_by_name, e.occurred_at '
            . 'FROM wms_stock_block_event e INNER JOIN wms_user_account u ON u.id = e.performed_by AND u.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId AND e.block_id = :blockId ORDER BY e.occurred_at, e.id',
            ['tenantId' => $tenantId, 'blockId' => $blockId],
        );
    }

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

    public function traceabilityEvents(string $tenantId, string $dimension, string $value): array
    {
        $column = match ($dimension) {
            'batch' => 'e.batch_number',
            'serial' => 'e.serial_number',
            default => throw new InvalidArgumentException('The traceability dimension is invalid.'),
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
}
