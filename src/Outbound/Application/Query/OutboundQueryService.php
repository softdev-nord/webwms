<?php

declare(strict_types=1);

namespace WebWMS\Outbound\Application\Query;

use Doctrine\DBAL\Connection;
use LogicException;

readonly class OutboundQueryService
{
    public function __construct(
        private Connection $connection
    ) {
    }

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

    public function shippingDocument(string $tenantId, string $documentId): ?array
    {
        $document = $this->connection->fetchAssociative('SELECT id, document_type, document_number, content, content_type FROM wms_shipping_document WHERE id = :id AND tenant_id = :tenantId', ['id' => $documentId, 'tenantId' => $tenantId]);

        return $document === false ? null : $document;
    }

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
                throw new LogicException('The allocation ID projection is invalid.');
            }

            $allocationIds[] = $id;
        }

        return $allocationIds;
    }

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
                throw new LogicException('The package projection is invalid.');
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
}
