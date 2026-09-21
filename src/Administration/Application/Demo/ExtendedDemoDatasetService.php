<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Demo;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class ExtendedDemoDatasetService
{
    public const int RECORDS_PER_ENTITY = 100;

    public function __construct(
        private Connection $connection
    ) {
    }

    public function generate(DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($now): void {
            for ($number = 1; $number <= self::RECORDS_PER_ENTITY; ++$number) {
                $createdAt = $now->modify(sprintf('-%d hours', self::RECORDS_PER_ENTITY - $number));
                $references = $this->createMasterData($number, $createdAt);
                $this->createInboundData($number, $createdAt, $references);
                $this->createOutboundData($number, $createdAt, $references);
                $this->createInventoryData($number, $createdAt, $references);
            }
        });
    }

    /** @return array<string, string> */
    private function createMasterData(int $number, DateTimeImmutable $createdAt): array
    {
        $suffix = sprintf('%03d', $number);
        $siteId = $this->id('site', $number);
        $warehouseId = $this->id('warehouse', $number);
        $areaId = $this->id('area', $number);
        $aisleId = $this->id('aisle', $number);
        $locationId = $this->id('location', $number);
        $productId = $this->id('product', $number);
        $supplierId = $this->id('supplier', $number);
        $strategyId = $this->id('putaway-strategy', $number);
        $selectionRuleId = $this->id('selection-rule', $number);
        $blockReasonId = $this->id('stock-block-reason', $number);

        $this->insert('wms_site', $siteId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'S' . $suffix,
            'name' => 'Demo-Standort ' . $suffix, 'timezone' => 'Europe/Berlin', 'status' => 'active',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_warehouse', $warehouseId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'site_id' => $siteId, 'code' => 'W' . $suffix,
            'name' => 'Demo-Lager ' . $suffix, 'warehouse_type' => $number % 4 === 0 ? 'high_bay' : 'standard',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_warehouse_area', $areaId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $warehouseId,
            'code' => 'AREA-' . $suffix, 'name' => 'Lagerbereich ' . $suffix,
            'area_type' => $number % 5 === 0 ? 'quality' : 'storage',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_warehouse_aisle', $aisleId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'area_id' => $areaId,
            'code' => 'G-' . $suffix, 'name' => 'Gang ' . $suffix,
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_storage_location', $locationId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $warehouseId,
            'code' => 'L-' . $suffix . '-01', 'created_at' => $this->date($createdAt),
            'putaway_enabled' => 1, 'putaway_priority' => $number, 'capacity_quantity' => 500,
            'area_id' => $areaId, 'aisle_id' => $aisleId, 'level_code' => sprintf('%02d', ($number % 5) + 1),
            'bin_code' => sprintf('%02d', ($number % 20) + 1), 'location_type' => 'storage',
            'created_by' => DemoBootstrapService::USER_ID,
        ]);
        $this->insert('wms_product_reference', $productId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'sku' => 'SKU-' . $suffix,
            'name' => 'Demo-Artikel ' . $suffix, 'created_at' => $this->date($createdAt),
            'weight_grams' => 250 + $number * 10, 'length_mm' => 100 + $number,
            'width_mm' => 80 + $number, 'height_mm' => 40 + $number,
        ]);
        $this->insert('wms_supplier', $supplierId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'SUP-' . $suffix,
            'name' => 'Demo-Lieferant ' . $suffix, 'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_putaway_strategy', $strategyId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $warehouseId,
            'code' => 'PUT-' . $suffix, 'stock_status' => 'available', 'location_prefix' => 'L-' . $suffix,
            'priority' => $number, 'enabled' => 1, 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_stock_selection_rule', $selectionRuleId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $warehouseId,
            'product_id' => $number % 4 === 0 ? $productId : null, 'code' => 'SEL-' . $suffix,
            'name' => 'Demo-Entnahmeregel ' . $suffix,
            'strategy' => ['fifo', 'lifo', 'fefo'][($number - 1) % 3], 'priority' => $number,
            'enabled' => 1, 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_stock_block_reason', $blockReasonId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'BLOCK-' . $suffix,
            'name' => 'Demo-Sperrgrund ' . $suffix, 'description' => 'Prüfgrund für den erweiterten Demodatensatz',
            'active' => 1, 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt),
        ]);

        $batchNumber = 'LOT-' . $suffix;
        $serialNumber = null;
        $expiresAt = $createdAt->modify(sprintf('+%d days', ($number % 120) - 20))->format('Y-m-d');
        $stockQuantity = 50 + $number;
        $stockKey = hash('sha256', implode('|', ['available', $batchNumber, '', $expiresAt]));
        $this->insertComposite('wms_stock_balance', [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $productId,
            'location_id' => $locationId, 'stock_key' => $stockKey, 'quantity' => $stockQuantity,
            'updated_at' => $this->date($createdAt), 'stock_status' => 'available',
            'batch_number' => $batchNumber, 'serial_number' => $serialNumber, 'expires_at' => $expiresAt,
        ], ['tenant_id', 'product_id', 'location_id', 'stock_key']);
        $this->insert('wms_stock_ledger', $this->id('stock-ledger', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $productId,
            'location_id' => $locationId, 'quantity_delta' => $stockQuantity, 'resulting_quantity' => $stockQuantity,
            'reason' => 'Erweiterter Demo-Anfangsbestand', 'performed_by' => DemoBootstrapService::USER_ID,
            'occurred_at' => $this->date($createdAt), 'stock_key' => $stockKey, 'stock_status' => 'available',
            'batch_number' => $batchNumber, 'serial_number' => $serialNumber, 'expires_at' => $expiresAt,
            'movement_type' => 'posting',
        ]);
        $serialNumber = 'SN-' . $suffix;
        $serialStockKey = hash('sha256', implode('|', ['available', '', $serialNumber, '']));
        $this->insertComposite('wms_stock_balance', [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $productId,
            'location_id' => $locationId, 'stock_key' => $serialStockKey, 'quantity' => 1,
            'updated_at' => $this->date($createdAt), 'stock_status' => 'available',
            'batch_number' => null, 'serial_number' => $serialNumber, 'expires_at' => null,
        ], ['tenant_id', 'product_id', 'location_id', 'stock_key']);
        $this->insert('wms_stock_ledger', $this->id('serial-stock-ledger', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $productId,
            'location_id' => $locationId, 'quantity_delta' => 1, 'resulting_quantity' => 1,
            'reason' => 'Demo-Serienbestand', 'performed_by' => DemoBootstrapService::USER_ID,
            'occurred_at' => $this->date($createdAt), 'stock_key' => $serialStockKey,
            'stock_status' => 'available', 'batch_number' => null, 'serial_number' => $serialNumber,
            'expires_at' => null, 'movement_type' => 'posting',
        ]);

        $specialStockTypeId = $this->id('special-stock-type', $number);
        $this->insert('wms_special_stock_type', $specialStockTypeId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'SST-' . $suffix,
            'name' => 'Demo-Sonderbestand ' . $suffix,
            'classification_kind' => $number % 2 === 0 ? 'owner' : 'special',
            'allocatable' => $number % 5 === 0 ? 0 : 1, 'active' => 1,
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
        ]);
        $this->insertComposite('wms_stock_classification', [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $productId,
            'location_id' => $locationId, 'stock_key' => $stockKey,
            'special_stock_type_id' => $specialStockTypeId,
            'owner_reference' => $number % 2 === 0 ? 'OWNER-' . $suffix : null,
            'reason' => 'Demo-Klassifizierung', 'changed_by' => DemoBootstrapService::USER_ID,
            'changed_at' => $this->date($createdAt),
        ], ['tenant_id', 'product_id', 'location_id', 'stock_key']);
        $this->insert('wms_stock_classification_event', $this->id('stock-classification-event', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $productId,
            'location_id' => $locationId, 'stock_key' => $stockKey,
            'special_stock_type_id' => $specialStockTypeId,
            'owner_reference' => $number % 2 === 0 ? 'OWNER-' . $suffix : null,
            'reason' => 'Demo-Klassifizierung', 'performed_by' => DemoBootstrapService::USER_ID,
            'occurred_at' => $this->date($createdAt),
        ]);

        return compact('warehouseId', 'locationId', 'productId', 'supplierId', 'strategyId', 'selectionRuleId', 'blockReasonId', 'stockKey', 'batchNumber', 'expiresAt');
    }

    /** @param array<string, string> $references */
    private function createInboundData(int $number, DateTimeImmutable $createdAt, array $references): void
    {
        $suffix = sprintf('%03d', $number);
        $purchaseOrderId = $this->id('purchase-order', $number);
        $purchaseItemId = $this->id('purchase-item', $number);
        $deliveryId = $this->id('inbound-delivery', $number);
        $deliveryLineId = $this->id('inbound-line', $number);
        $receiptId = $this->id('inbound-receipt', $number);
        $status = ['advised', 'receiving', 'completed'][$number % 3];

        $this->insert('wms_purchase_order', $purchaseOrderId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'PO-' . $suffix,
            'supplier_reference' => 'SUP-' . $suffix, 'status' => $status,
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_purchase_order_item', $purchaseItemId, [
            'purchase_order_id' => $purchaseOrderId, 'product_id' => $references['productId'],
            'ordered_quantity' => 10 + $number, 'advised_quantity' => 10 + $number,
            'received_quantity' => $number % 3 === 2 ? 10 + $number : 0, 'status' => $status,
        ]);
        $this->insert('wms_inbound_delivery', $deliveryId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'purchase_order_id' => $purchaseOrderId,
            'code' => 'IN-' . $suffix, 'delivery_note' => 'LS-' . $suffix,
            'expected_at' => $this->date($createdAt->modify('+2 days')), 'status' => $status,
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_inbound_delivery_line', $deliveryLineId, [
            'inbound_delivery_id' => $deliveryId, 'purchase_order_item_id' => $purchaseItemId,
            'advised_quantity' => 10 + $number, 'status' => $status,
        ]);
        $this->insert('wms_inbound_receipt', $receiptId, [
            'inbound_delivery_line_id' => $deliveryLineId, 'quantity' => 10 + $number,
            'status' => $number % 4 === 0 ? 'inspection_required' : 'accepted',
            'quality_decision' => $number % 4 === 0 ? null : 'accepted', 'stock_status' => 'available',
            'location_id' => $references['locationId'], 'received_by' => DemoBootstrapService::USER_ID,
            'received_at' => $this->date($createdAt),
        ]);
        $this->insertComposite('wms_inbound_quality_answer', [
            'receipt_id' => $receiptId, 'position' => 1, 'question' => 'Verpackung unbeschädigt?',
            'passed' => $number % 4 === 0 ? 0 : 1, 'note' => $number % 4 === 0 ? 'Prüfung erforderlich' : 'Ohne Befund',
        ], ['receipt_id', 'position']);
        $this->insert('wms_inbound_discrepancy', $this->id('inbound-discrepancy', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'receipt_id' => $receiptId,
            'discrepancy_type' => 'quantity', 'expected_quantity' => 10 + $number,
            'actual_quantity' => 9 + $number, 'reason' => 'Demo-Mengendifferenz',
            'status' => $number % 2 === 0 ? 'open' : 'resolved',
            'resolution_note' => $number % 2 === 0 ? null : 'Differenz akzeptiert',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'resolved_by' => $number % 2 === 0 ? null : DemoBootstrapService::USER_ID,
            'resolved_at' => $number % 2 === 0 ? null : $this->date($createdAt),
        ]);
        $this->insert('wms_putaway_order', $this->id('putaway-order', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'inbound_receipt_id' => $receiptId,
            'strategy_id' => $references['strategyId'], 'product_id' => $references['productId'],
            'source_location_id' => $references['locationId'], 'target_location_id' => $references['locationId'],
            'quantity' => 10 + $number, 'stock_status' => 'available',
            'status' => $number % 2 === 0 ? 'open' : 'completed', 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt),
            'confirmed_by' => $number % 2 === 0 ? null : DemoBootstrapService::USER_ID,
            'confirmed_at' => $number % 2 === 0 ? null : $this->date($createdAt),
        ]);
        $unplannedId = $this->id('unplanned-receipt', $number);
        $this->insert('wms_unplanned_receipt', $unplannedId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'supplier_id' => $references['supplierId'],
            'code' => 'UNP-' . $suffix, 'delivery_note' => 'UNP-LS-' . $suffix,
            'status' => $number % 2 === 0 ? 'accepted' : 'booked', 'accepted_by' => DemoBootstrapService::USER_ID,
            'accepted_at' => $this->date($createdAt),
            'booked_by' => $number % 2 === 0 ? null : DemoBootstrapService::USER_ID,
            'booked_at' => $number % 2 === 0 ? null : $this->date($createdAt),
        ]);
        $this->insert('wms_unplanned_receipt_item', $this->id('unplanned-item', $number), [
            'receipt_id' => $unplannedId, 'product_id' => $references['productId'],
            'location_id' => $references['locationId'], 'quantity' => 3 + $number,
            'stock_status' => 'available', 'batch_number' => 'UNP-' . $suffix,
        ]);
    }

    /** @param array<string, string> $references */
    private function createOutboundData(int $number, DateTimeImmutable $createdAt, array $references): void
    {
        $suffix = sprintf('%03d', $number);
        $orderId = $this->id('outbound-order', $number);
        $reservationId = $this->id('reservation', $number);
        $allocationId = $this->id('allocation', $number);
        $pickListId = $this->id('pick-list', $number);
        $pickTaskId = $this->id('pick-task', $number);
        $packingId = $this->id('packing-order', $number);
        $packageId = $this->id('package', $number);
        $shipmentId = $this->id('shipment', $number);
        $manifestId = $this->id('manifest', $number);
        $completed = $number % 4 === 0;

        $this->insert('wms_stock_reservation', $reservationId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'product_id' => $references['productId'],
            'order_reference' => 'SO-' . $suffix, 'requested_quantity' => 2, 'allocated_quantity' => 2,
            'status' => 'allocated', 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt), 'updated_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_stock_allocation', $allocationId, [
            'reservation_id' => $reservationId, 'tenant_id' => DemoBootstrapService::TENANT_ID,
            'product_id' => $references['productId'], 'location_id' => $references['locationId'],
            'stock_key' => $references['stockKey'], 'stock_status' => 'available', 'quantity' => 2,
            'status' => $completed ? 'consumed' : 'active', 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_stock_selection_event', $this->id('selection-event', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'rule_id' => $references['selectionRuleId'],
            'reservation_id' => $reservationId, 'product_id' => $references['productId'],
            'requested_quantity' => 2, 'allocated_quantity' => 2, 'candidate_count' => 1,
            'performed_by' => DemoBootstrapService::USER_ID, 'occurred_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_outbound_order', $orderId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'order_number' => 'SO-' . $suffix,
            'customer_reference' => 'CUSTOMER-' . $suffix, 'status' => $completed ? 'completed' : 'released',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'released_by' => DemoBootstrapService::USER_ID, 'released_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_outbound_order_item', $this->id('outbound-item', $number), [
            'outbound_order_id' => $orderId, 'product_id' => $references['productId'],
            'requested_quantity' => 2, 'reservation_id' => $reservationId,
        ]);
        $this->insert('wms_pick_list', $pickListId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'PICK-' . $suffix,
            'status' => $completed ? 'completed' : 'released', 'assigned_to' => DemoBootstrapService::USER_ID,
            'assigned_by' => DemoBootstrapService::USER_ID, 'assigned_at' => $this->date($createdAt),
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_pick_task', $pickTaskId, [
            'pick_list_id' => $pickListId, 'allocation_id' => $allocationId, 'sequence_number' => 1,
            'status' => $completed ? 'confirmed' : 'open',
            'confirmed_by' => $completed ? DemoBootstrapService::USER_ID : null,
            'confirmed_at' => $completed ? $this->date($createdAt) : null,
        ]);
        $this->insert('wms_packing_order', $packingId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'pick_list_id' => $pickListId,
            'code' => 'PACK-' . $suffix, 'status' => $completed ? 'completed' : 'open',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
            'completed_by' => $completed ? DemoBootstrapService::USER_ID : null,
            'completed_at' => $completed ? $this->date($createdAt) : null,
        ]);
        $this->insert('wms_package', $packageId, [
            'packing_order_id' => $packingId, 'package_number' => 'PKG-' . $suffix,
            'weight_grams' => 1000 + $number * 10, 'status' => $completed ? 'closed' : 'open',
            'packed_by' => DemoBootstrapService::USER_ID, 'packed_at' => $this->date($createdAt),
        ]);
        $this->insertComposite('wms_package_item', ['package_id' => $packageId, 'pick_task_id' => $pickTaskId], ['package_id', 'pick_task_id']);
        $this->insert('wms_shipment', $shipmentId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'packing_order_id' => $packingId,
            'shipment_number' => 'SHIP-' . $suffix, 'carrier' => $number % 2 === 0 ? 'DHL' : 'DPD',
            'service' => 'Standard', 'status' => $completed ? 'dispatched' : 'created',
            'tracking_number' => 'TRACK-' . $suffix, 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt), 'updated_at' => $this->date($createdAt),
            'dispatched_by' => $completed ? DemoBootstrapService::USER_ID : null,
            'dispatched_at' => $completed ? $this->date($createdAt) : null,
        ]);
        $this->insert('wms_loading_manifest', $manifestId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'LOAD-' . $suffix,
            'tour_reference' => 'TOUR-' . sprintf('%02d', ($number % 20) + 1),
            'vehicle_reference' => 'HH-WMS-' . $suffix, 'status' => $completed ? 'completed' : 'open',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
            'completed_by' => $completed ? DemoBootstrapService::USER_ID : null,
            'completed_at' => $completed ? $this->date($createdAt) : null,
        ]);
        $this->insertComposite('wms_loading_manifest_shipment', [
            'manifest_id' => $manifestId, 'shipment_id' => $shipmentId,
            'status' => $completed ? 'loaded' : 'assigned',
            'loaded_by' => $completed ? DemoBootstrapService::USER_ID : null,
            'loaded_at' => $completed ? $this->date($createdAt) : null,
        ], ['manifest_id', 'shipment_id']);
    }

    /** @param array<string, string> $references */
    private function createInventoryData(int $number, DateTimeImmutable $createdAt, array $references): void
    {
        $suffix = sprintf('%03d', $number);
        $returnId = $this->id('return-order', $number);
        $returnItemId = $this->id('return-item', $number);
        $policyId = $this->id('replenishment-policy', $number);
        $countId = $this->id('inventory-count', $number);
        $cycleId = $this->id('cycle-count', $number);
        $blockId = $this->id('stock-block', $number);

        $blockedStockKey = hash('sha256', implode('|', ['blocked', $references['batchNumber'], '', $references['expiresAt']]));
        $this->insert('wms_stock_block', $blockId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'reason_id' => $references['blockReasonId'],
            'product_id' => $references['productId'], 'location_id' => $references['locationId'],
            'source_stock_key' => $references['stockKey'], 'blocked_stock_key' => $blockedStockKey,
            'original_status' => 'available', 'batch_number' => $references['batchNumber'],
            'serial_number' => null, 'expires_at' => $references['expiresAt'], 'quantity' => 1,
            'note' => 'Historische Demo-Sperre', 'status' => 'released',
            'blocked_by' => DemoBootstrapService::USER_ID, 'blocked_at' => $this->date($createdAt),
            'reviewed_by' => DemoBootstrapService::USER_ID, 'reviewed_at' => $this->date($createdAt->modify('+10 minutes')),
            'review_note' => 'Demo-Prüfung ohne Befund', 'released_by' => DemoBootstrapService::USER_ID,
            'released_at' => $this->date($createdAt->modify('+20 minutes')),
        ]);
        foreach (['blocked' => 'Historische Demo-Sperre', 'reviewed' => 'Demo-Prüfung ohne Befund', 'released' => 'Demo-Freigabe'] as $eventType => $eventNote) {
            $offset = $eventType === 'blocked' ? 0 : ($eventType === 'reviewed' ? 10 : 20);
            $this->insert('wms_stock_block_event', $this->id('stock-block-' . $eventType, $number), [
                'tenant_id' => DemoBootstrapService::TENANT_ID, 'block_id' => $blockId,
                'event_type' => $eventType, 'note' => $eventNote,
                'performed_by' => DemoBootstrapService::USER_ID,
                'occurred_at' => $this->date($createdAt->modify(sprintf('+%d minutes', $offset))),
            ]);
        }

        $this->insert('wms_return_order', $returnId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'code' => 'RET-' . $suffix,
            'order_reference' => 'SO-' . $suffix, 'status' => $number % 2 === 0 ? 'open' : 'completed',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'updated_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_return_item', $returnItemId, [
            'return_order_id' => $returnId, 'product_id' => $references['productId'],
            'expected_quantity' => 1, 'reason' => 'Demo-Rücksendung',
            'status' => $number % 2 === 0 ? 'expected' : 'received',
        ]);
        $this->insert('wms_return_receipt', $this->id('return-receipt', $number), [
            'return_item_id' => $returnItemId, 'quantity' => 1, 'status' => 'inspected',
            'quality_decision' => $number % 5 === 0 ? 'rejected' : 'accepted',
            'stock_status' => $number % 5 === 0 ? 'blocked' : 'available',
            'location_id' => $references['locationId'], 'inspection_note' => 'Demo-Prüfung',
            'received_by' => DemoBootstrapService::USER_ID, 'received_at' => $this->date($createdAt),
            'inspected_by' => DemoBootstrapService::USER_ID, 'inspected_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_replenishment_policy', $policyId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $references['warehouseId'],
            'product_id' => $references['productId'], 'target_location_id' => $references['locationId'],
            'code' => 'REP-' . $suffix, 'source_location_prefix' => 'L-', 'minimum_quantity' => 10,
            'target_quantity' => 50, 'priority' => $number, 'enabled' => 1,
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_replenishment_order', $this->id('replenishment-order', $number), [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'policy_id' => $policyId,
            'product_id' => $references['productId'], 'source_location_id' => $references['locationId'],
            'target_location_id' => $references['locationId'], 'quantity' => 20,
            'stock_key' => $references['stockKey'], 'stock_status' => 'available',
            'status' => $number % 2 === 0 ? 'open' : 'completed',
            'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
            'confirmed_by' => $number % 2 === 0 ? null : DemoBootstrapService::USER_ID,
            'confirmed_at' => $number % 2 === 0 ? null : $this->date($createdAt),
        ]);
        $this->insert('wms_cycle_count_plan', $cycleId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $references['warehouseId'],
            'code' => 'CYCLE-' . $suffix, 'location_prefix' => 'L-' . $suffix,
            'interval_days' => 30, 'next_due_at' => $this->date($createdAt->modify('+30 days')),
            'active' => 1, 'created_by' => DemoBootstrapService::USER_ID, 'created_at' => $this->date($createdAt),
        ]);
        $this->insert('wms_inventory_count', $countId, [
            'tenant_id' => DemoBootstrapService::TENANT_ID, 'warehouse_id' => $references['warehouseId'],
            'code' => 'COUNT-' . $suffix, 'location_prefix' => 'L-' . $suffix,
            'status' => $number % 2 === 0 ? 'open' : 'submitted', 'line_count' => 1,
            'difference_count' => $number % 5 === 0 ? 1 : 0, 'created_by' => DemoBootstrapService::USER_ID,
            'created_at' => $this->date($createdAt), 'count_type' => 'cycle_count',
            'cycle_count_plan_id' => $cycleId,
        ]);
        $this->insert('wms_inventory_count_line', $this->id('inventory-line', $number), [
            'inventory_count_id' => $countId, 'product_id' => $references['productId'],
            'location_id' => $references['locationId'], 'stock_key' => $references['stockKey'],
            'stock_status' => 'available', 'expected_quantity' => 50 + $number,
            'counted_quantity' => $number % 2 === 0 ? null : 50 + $number,
            'difference_quantity' => $number % 2 === 0 ? null : 0,
            'counted_by' => $number % 2 === 0 ? null : DemoBootstrapService::USER_ID,
            'counted_at' => $number % 2 === 0 ? null : $this->date($createdAt),
        ]);
    }

    /** @param array<string, mixed> $data */
    private function insert(string $table, string $id, array $data): void
    {
        if ($this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id', $table), ['id' => $id]) === false) {
            $this->connection->insert($table, ['id' => $id, ...$data]);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string>         $keyColumns
     */
    private function insertComposite(string $table, array $data, array $keyColumns): void
    {
        $where = [];
        $parameters = [];
        foreach ($keyColumns as $column) {
            $where[] = $column . ' = :' . $column;
            $parameters[$column] = $data[$column];
        }
        if ($this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE %s', $table, implode(' AND ', $where)), $parameters) === false) {
            $this->connection->insert($table, $data);
        }
    }

    private function id(string $entity, int $number): string
    {
        return Uuid::v5(Uuid::fromString(DemoBootstrapService::TENANT_ID), sprintf('extended-demo:%s:%03d', $entity, $number))->toRfc4122();
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
