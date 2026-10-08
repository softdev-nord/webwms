<?php

declare(strict_types=1);

namespace WebWMS\Installation\Application;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class V3DemoConfigurationInstaller
{
    public function install(Connection $connection, string $tenantId, string $adminId, string $date): void
    {
        $warehouseId = (string) $connection->fetchOne('SELECT id FROM wms_warehouse WHERE tenant_id = ? ORDER BY code LIMIT 1', [$tenantId]);
        if ($warehouseId === '') {
            return;
        }

        $connection->insert('wms_putaway_strategy', [
            'id' => $this->id($tenantId, 'putaway-default'), 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId,
            'code' => 'V2-STANDARD', 'stock_status' => 'available', 'location_prefix' => '',
            'priority' => 100, 'enabled' => 1, 'created_by' => $adminId, 'created_at' => $date,
        ]);
        $connection->insert('wms_stock_selection_rule', [
            'id' => $this->id($tenantId, 'selection-fifo'), 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId,
            'product_id' => null, 'code' => 'FIFO-STANDARD', 'name' => 'FIFO Standardentnahme',
            'strategy' => 'fifo', 'priority' => 100, 'enabled' => 1, 'created_by' => $adminId, 'created_at' => $date,
        ]);
        $connection->insert('wms_stock_block_reason', [
            'id' => $this->id($tenantId, 'block-quality'), 'tenant_id' => $tenantId, 'code' => 'QUALITY',
            'name' => 'Qualitätsprüfung', 'description' => 'Demokonfiguration für gesperrte Prüfbestände',
            'active' => 1, 'created_by' => $adminId, 'created_at' => $date,
        ]);
        $connection->insert('wms_tenant_context', [
            'id' => $this->id($tenantId, 'context-standard'), 'tenant_id' => $tenantId, 'business_partner_id' => null,
            'code' => 'STANDARD', 'name' => 'Standard-Lagerkontext', 'active' => 1,
            'created_by' => $adminId, 'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ]);
        foreach ([
            ['INBOUND', 'Wareneingänge', 'inbound_delivery', 'WE-', 6],
            ['OUTBOUND', 'Warenausgänge', 'outbound_order', 'WA-', 6],
            ['TRANSPORT', 'Transporte', 'transport_order', 'TR-', 6],
        ] as [$code, $name, $type, $prefix, $padding]) {
            $connection->insert('wms_number_range', [
                'id' => $this->id($tenantId, 'number-range-' . strtolower($code)), 'tenant_id' => $tenantId,
                'code' => $code, 'name' => $name, 'object_type' => $type, 'prefix' => $prefix, 'suffix' => '',
                'padding' => $padding, 'next_value' => 1, 'maximum_value' => null, 'gs1_company_prefix' => null,
                'enabled' => 1, 'created_by' => $adminId, 'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
            ]);
        }
        $connection->insert('wms_device_profile', [
            'id' => $this->id($tenantId, 'device-mobile'), 'tenant_id' => $tenantId, 'code' => 'MOBILE',
            'name' => 'Mobiler Lagerdialog', 'device_type' => 'scanner', 'start_route' => '/v3/inventory/stock',
            'fullscreen' => 1, 'scan_suffix' => 'ENTER', 'enabled' => 1,
            'created_by' => $adminId, 'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ]);

        $this->installScreenshotData($connection, $tenantId, $adminId, $date);
    }

    public function installScreenshotData(Connection $connection, string $tenantId, string $adminId, string $date): void
    {
        $productId = $this->requiredId($connection, 'wms_product_reference', $tenantId);
        $locationId = $this->requiredId($connection, 'wms_storage_location', $tenantId);
        $supplierId = $this->requiredId($connection, 'wms_supplier', $tenantId);
        $pickListId = $this->id($tenantId, 'screenshot-pick-list');
        $this->insert($connection, 'wms_pick_list', [
            'id' => $pickListId, 'tenant_id' => $tenantId, 'code' => 'HB-PL-001', 'status' => 'released',
            'assigned_to' => $adminId, 'assigned_by' => $adminId, 'assigned_at' => $date,
            'created_by' => $adminId, 'created_at' => $date, 'updated_at' => $date,
        ]);
        $stock = $connection->fetchAssociative(
            'SELECT product_id, location_id, stock_key FROM wms_stock_balance WHERE tenant_id = ? AND quantity > 0 ORDER BY product_id, location_id, stock_key LIMIT 1',
            [$tenantId],
        );
        if (!is_array($stock)) {
            throw new \RuntimeException('Screenshot demo data requires a positive stock position.');
        }

        $apiClientId = $this->id($tenantId, 'screenshot-api-client');
        $this->insert($connection, 'wms_api_client', [
            'id' => $apiClientId, 'tenant_id' => $tenantId, 'acting_user_id' => $adminId,
            'name' => 'Handbuch Demo-Client', 'secret_hash' => hash('sha256', 'handbook-demo-client'),
            'permissions' => json_encode(['documentation.handbook.read'], JSON_THROW_ON_ERROR),
            'active' => 1, 'created_at' => $date, 'last_used_at' => null,
        ]);

        $blockId = $this->id($tenantId, 'screenshot-stock-block');
        $reasonId = $this->requiredId($connection, 'wms_stock_block_reason', $tenantId);
        $this->insert($connection, 'wms_stock_block', [
            'id' => $blockId, 'tenant_id' => $tenantId, 'reason_id' => $reasonId,
            'product_id' => (string) $stock['product_id'], 'location_id' => (string) $stock['location_id'],
            'source_stock_key' => (string) $stock['stock_key'], 'blocked_stock_key' => hash('sha256', 'blocked|||'),
            'original_status' => 'available', 'batch_number' => null, 'serial_number' => null, 'expires_at' => null,
            'quantity' => 1, 'note' => 'Demodatensatz für das Benutzerhandbuch', 'status' => 'active',
            'blocked_by' => $adminId, 'blocked_at' => $date, 'reviewed_by' => null, 'reviewed_at' => null,
            'review_note' => null, 'released_by' => null, 'released_at' => null,
        ]);

        $receiptId = $this->id($tenantId, 'screenshot-unplanned-receipt');
        $this->insert($connection, 'wms_unplanned_receipt', [
            'id' => $receiptId, 'tenant_id' => $tenantId, 'supplier_id' => $supplierId,
            'code' => 'HB-WE-001', 'delivery_note' => 'HB-LS-001', 'status' => 'accepted',
            'accepted_by' => $adminId, 'accepted_at' => $date, 'booked_by' => null, 'booked_at' => null,
        ]);

        $packingId = $this->id($tenantId, 'screenshot-packing-order');
        $this->insert($connection, 'wms_packing_order', [
            'id' => $packingId, 'tenant_id' => $tenantId, 'pick_list_id' => $pickListId,
            'code' => 'HB-PA-001', 'status' => 'packing', 'created_by' => $adminId,
            'created_at' => $date, 'updated_at' => $date, 'completed_by' => null, 'completed_at' => null,
        ]);
        $shipmentId = $this->id($tenantId, 'screenshot-shipment');
        $this->insert($connection, 'wms_shipment', [
            'id' => $shipmentId, 'tenant_id' => $tenantId, 'packing_order_id' => $packingId,
            'shipment_number' => 'HB-SH-001', 'carrier' => 'DHL', 'service' => 'Parcel', 'status' => 'prepared',
            'tracking_number' => 'HB0000000001', 'label_reference' => null, 'handover_reference' => null,
            'created_by' => $adminId, 'created_at' => $date, 'updated_at' => $date,
            'label_registered_by' => null, 'label_registered_at' => null, 'dispatched_by' => null, 'dispatched_at' => null,
        ]);
        $manifestId = $this->id($tenantId, 'screenshot-loading-manifest');
        $this->insert($connection, 'wms_loading_manifest', [
            'id' => $manifestId, 'tenant_id' => $tenantId, 'code' => 'HB-LM-001',
            'tour_reference' => 'HB-TOUR-001', 'vehicle_reference' => 'HH-WM 3000', 'status' => 'open',
            'created_by' => $adminId, 'created_at' => $date, 'updated_at' => $date,
            'completed_by' => null, 'completed_at' => null,
        ]);
        $this->insert($connection, 'wms_loading_manifest_shipment', [
            'manifest_id' => $manifestId, 'shipment_id' => $shipmentId, 'status' => 'pending',
            'loaded_by' => null, 'loaded_at' => null,
        ], 'manifest_id', $manifestId);

        $erpId = $this->id($tenantId, 'screenshot-erp');
        $this->insert($connection, 'wms_erp_connection', $this->connectionData($erpId, $tenantId, $adminId, $date, 'Handbuch ERP', 'https://erp.example.invalid/api'));
        $carrierId = $this->id($tenantId, 'screenshot-carrier');
        $this->insert($connection, 'wms_carrier_connection', [
            'id' => $carrierId, 'tenant_id' => $tenantId, 'name' => 'Handbuch Carrier', 'carrier_code' => 'HANDBOOK',
            'endpoint_url' => 'https://carrier.example.invalid/api', 'credential_env' => 'HANDBOOK_CARRIER_TOKEN',
            'active' => 1, 'created_by' => $adminId, 'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ]);

        $deviceId = $this->id($tenantId, 'screenshot-device');
        $this->insert($connection, 'wms_device', [
            'id' => $deviceId, 'tenant_id' => $tenantId, 'code' => 'HB-SCANNER', 'name' => 'Handbuch Scanner',
            'device_type' => 'scanner', 'active' => 1, 'created_by' => $adminId, 'created_at' => $date,
            'changed_by' => null, 'changed_at' => null,
        ]);
        $this->insert($connection, 'wms_scan_event', [
            'id' => $this->id($tenantId, 'screenshot-scan'), 'tenant_id' => $tenantId, 'device_id' => $deviceId,
            'scan_type' => 'barcode', 'scan_value' => 'HB-ARTIKEL-001', 'process_type' => 'inventory',
            'context_reference' => 'HB-DEMO', 'request_id' => 'hb-scan-001', 'status' => 'processed',
            'message' => 'Erfolgreich verarbeitet', 'scanned_by' => $adminId, 'scanned_at' => $date,
        ]);

        $measurementDeviceId = $this->id($tenantId, 'screenshot-measurement-device');
        $this->insert($connection, 'wms_measurement_device', [
            'id' => $measurementDeviceId, 'tenant_id' => $tenantId, 'code' => 'HB-SCALE', 'name' => 'Handbuch Waage',
            'device_type' => 'scale', 'active' => 1, 'created_by' => $adminId, 'created_at' => $date,
            'changed_by' => null, 'changed_at' => null,
        ]);
        $this->insert($connection, 'wms_measurement', [
            'id' => $this->id($tenantId, 'screenshot-measurement'), 'tenant_id' => $tenantId,
            'device_id' => $measurementDeviceId, 'target_type' => 'product', 'target_id' => $productId,
            'weight_grams' => 1250, 'length_mm' => 300, 'width_mm' => 200, 'height_mm' => 150,
            'request_id' => 'hb-measurement-001', 'status' => 'processed', 'message' => 'Messung abgeschlossen',
            'measured_by' => $adminId, 'measured_at' => $date,
        ]);

        $printerId = $this->id($tenantId, 'screenshot-printer');
        $this->insert($connection, 'wms_printer', $this->connectionData($printerId, $tenantId, $adminId, $date, 'Handbuch Drucker', 'ipp://printer.example.invalid'));
        $this->insert($connection, 'wms_print_job', [
            'id' => $this->id($tenantId, 'screenshot-print-job'), 'tenant_id' => $tenantId, 'printer_id' => $printerId,
            'document_type' => 'shipping_label', 'document_reference' => 'HB-SH-001', 'format' => 'PDF',
            'copies' => 1, 'idempotency_key' => 'hb-print-001', 'status' => 'completed', 'attempts' => 1,
            'external_reference' => 'HB-PRINT-001', 'last_error' => null, 'created_by' => $adminId,
            'created_at' => $date, 'completed_at' => $date,
        ]);

        $wcsId = $this->id($tenantId, 'screenshot-wcs');
        $this->insert($connection, 'wms_wcs_connection', [
            'id' => $wcsId, 'tenant_id' => $tenantId, 'code' => 'HB-WCS', 'name' => 'Handbuch WCS',
            'system_type' => 'shuttle', 'endpoint_url' => 'https://wcs.example.invalid/api',
            'credential_env' => 'HANDBOOK_WCS_TOKEN', 'active' => 1, 'created_by' => $adminId,
            'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ]);
        $this->insert($connection, 'wms_machine_command', [
            'id' => $this->id($tenantId, 'screenshot-machine-command'), 'tenant_id' => $tenantId,
            'connection_id' => $wcsId, 'command_type' => 'move', 'source' => 'HB-QUELLE',
            'destination' => 'HB-ZIEL', 'load_unit' => 'HB-LU-001', 'request_id' => 'hb-wcs-001',
            'status' => 'completed', 'message' => 'Transport abgeschlossen', 'created_by' => $adminId,
            'created_at' => $date, 'changed_by' => $adminId, 'changed_at' => $date,
        ]);

        $this->insert($connection, 'wms_extension_work_item', [
            'id' => $this->id($tenantId, 'screenshot-extension-work-item'), 'tenant_id' => $tenantId,
            'workflow_type' => 'container_cycle', 'reference' => 'HB-CONTAINER-001', 'status' => 'available',
            'payload_json' => json_encode(['container' => 'HB-CONTAINER-001'], JSON_THROW_ON_ERROR),
            'created_by' => $adminId, 'created_at' => $date, 'changed_by' => $adminId, 'changed_at' => $date,
        ]);
        $this->insert($connection, 'wms_integration_outbox', [
            'id' => $this->id($tenantId, 'screenshot-outbox'), 'tenant_id' => $tenantId,
            'event_name' => 'handbook.demo.created', 'aggregate_type' => 'documentation',
            'aggregate_id' => $productId, 'payload' => json_encode(['reference' => 'HB-DEMO'], JSON_THROW_ON_ERROR),
            'status' => 'published', 'occurred_at' => $date, 'created_by' => $adminId,
            'acknowledged_by' => $adminId, 'acknowledged_at' => $date,
        ]);

        $automationDeviceId = $this->id($tenantId, 'screenshot-automation-device');
        $this->insert($connection, 'wms_automation_device', [
            'id' => $automationDeviceId, 'tenant_id' => $tenantId, 'code' => 'HB-AUTO', 'name' => 'Handbuch Fördertechnik',
            'device_type' => 'conveyor', 'endpoint_url' => 'https://automation.example.invalid/api',
            'credential_env' => 'HANDBOOK_AUTOMATION_TOKEN', 'active' => 1, 'created_by' => $adminId,
            'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ]);
        $this->insert($connection, 'wms_device_command', [
            'id' => $this->id($tenantId, 'screenshot-device-command'), 'tenant_id' => $tenantId,
            'device_id' => $automationDeviceId, 'command_type' => 'transport', 'location_id' => $locationId,
            'reference_type' => 'product', 'reference_id' => $productId, 'request_id' => 'hb-auto-001',
            'status' => 'completed', 'message' => 'Auftrag abgeschlossen', 'created_by' => $adminId,
            'created_at' => $date, 'changed_by' => $adminId, 'changed_at' => $date,
        ]);
        $this->insert($connection, 'wms_transport_endpoint', [
            'id' => $this->id($tenantId, 'screenshot-transport-endpoint'), 'tenant_id' => $tenantId,
            'code' => 'HB-REST', 'name' => 'Handbuch REST-Endpunkt', 'adapter_type' => 'rest',
            'address' => 'https://transport.example.invalid/messages', 'credential_env' => 'HANDBOOK_TRANSPORT_TOKEN',
            'active' => 1, 'created_by' => $adminId, 'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ]);
    }

    /** @param array<string, mixed> $data */
    private function insert(Connection $connection, string $table, array $data, string $idColumn = 'id', ?string $id = null): void
    {
        $id ??= (string) $data[$idColumn];
        if ((int) $connection->fetchOne(sprintf('SELECT COUNT(*) FROM %s WHERE %s = ?', $table, $idColumn), [$id]) > 0) {
            return;
        }

        $connection->insert($table, $data);
    }

    private function requiredId(Connection $connection, string $table, string $tenantId): string
    {
        $id = $connection->fetchOne(sprintf('SELECT id FROM %s WHERE tenant_id = ? ORDER BY id LIMIT 1', $table), [$tenantId]);
        if (!is_string($id) || $id === '') {
            throw new \RuntimeException(sprintf('Screenshot demo data requires a row in "%s".', $table));
        }

        return $id;
    }

    /** @return array<string, mixed> */
    private function connectionData(string $id, string $tenantId, string $adminId, string $date, string $name, string $endpoint): array
    {
        return [
            'id' => $id, 'tenant_id' => $tenantId, 'name' => $name, 'endpoint_url' => $endpoint,
            'credential_env' => 'HANDBOOK_DEMO_TOKEN', 'active' => 1, 'created_by' => $adminId,
            'created_at' => $date, 'changed_by' => null, 'changed_at' => null,
        ];
    }

    private function id(string $tenantId, string $name): string
    {
        return Uuid::v5(Uuid::fromString($tenantId), 'v3-demo:' . $name)->toRfc4122();
    }
}
