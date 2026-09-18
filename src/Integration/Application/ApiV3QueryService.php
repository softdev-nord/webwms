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
            "SELECT CONCAT(b.product_id, '|', b.location_id, '|', b.stock_key) cursor, "
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
}
