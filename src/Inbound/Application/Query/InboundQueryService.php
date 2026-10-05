<?php

declare(strict_types=1);

namespace WebWMS\Inbound\Application\Query;

use Doctrine\DBAL\Connection;

readonly class InboundQueryService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function receivingLocations(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT l.id, l.code, w.code warehouse_code FROM wms_storage_location l '
            . 'INNER JOIN wms_warehouse w ON w.id = l.warehouse_id AND w.tenant_id = l.tenant_id '
            . 'WHERE l.tenant_id = :tenantId ORDER BY w.code, l.code',
            ['tenantId' => $tenantId],
        );
    }

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

    public function plannedInboundWorklist(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT d.id delivery_id, d.code delivery_code, d.delivery_note, d.expected_at, d.status delivery_status, '
            . 'd.sender_name, d.sender_street, d.sender_postal_code, d.sender_city, d.sender_country_code, '
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
}
