<?php

declare(strict_types=1);

namespace WebWMS\Installation\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

readonly class V2DemoDataInstaller
{
    public function __construct(
        private string $projectDir,
    ) {
    }

    public function install(Connection $connection, string $tenantId, string $siteId, string $adminId, DateTimeImmutable $now): int
    {
        $data = $this->data();
        $warehouseId = $this->id($tenantId, 'warehouse', 'v2');
        $date = $now->format('Y-m-d H:i:s.u');
        $connection->insert('wms_warehouse', [
            'id' => $warehouseId, 'tenant_id' => $tenantId, 'site_id' => $siteId,
            'code' => 'V2-LAGER', 'name' => 'V2-Demolager', 'warehouse_type' => 'standard',
            'created_by' => $adminId, 'created_at' => $date,
        ]);

        /** @var array<string, string> $areaIds */
        $areaIds = [];
        /** @var array<int, string> $aisleIds */
        $aisleIds = [];
        /** @var array<int, array<string, mixed>> $layouts */
        $layouts = [];
        foreach ($data['stock_layout'] as $layout) {
            $number = (int) $layout['stock_nr'];
            $type = strtoupper((string) $layout['stock_typ']);
            $areaIds[$type] ??= $this->createArea($connection, $tenantId, $warehouseId, $adminId, $type, (string) $layout['stock_long_description'], $date);
            $aisleIds[$number] = $this->createAisle($connection, $tenantId, $areaIds[$type], $adminId, $number, (string) $layout['stock_description'], (string) $layout['stock_model'], $date);
            $layouts[$number] = $layout;
        }

        /** @var array<string, array<string, mixed>> $locationOverlay */
        $locationOverlay = [];
        foreach ($data['stock_location'] as $location) {
            $coordinate = (string) $location['stock_location_coordinate'];
            $locationOverlay[$coordinate] = $location;
            $number = (int) $location['stock_location_ln'];
            if (!isset($layouts[$number])) {
                $zone = strtoupper((string) $location['stock_location_zone']);
                $areaIds[$zone] ??= $this->createArea($connection, $tenantId, $warehouseId, $adminId, $zone, $zone, $date);
                $aisleIds[$number] ??= $this->createAisle($connection, $tenantId, $areaIds[$zone], $adminId, $number, 'Rekonstruierte V2-Lagerstruktur', 'V2', $date);
            }
        }

        $locationCount = 0;
        foreach ($layouts as $number => $layout) {
            $type = strtoupper((string) $layout['stock_typ']);
            $locationCount += $this->createGrid(
                $connection,
                $tenantId,
                $warehouseId,
                $areaIds[$type],
                $aisleIds[$number],
                $number,
                (int) $layout['stock_level1'],
                (int) $layout['stock_level2'],
                (int) $layout['stock_level3'],
                (string) ($layout['stock_long_description'] ?? $layout['stock_description']),
                $type,
                $locationOverlay,
                $adminId,
                $date,
            );
        }
        foreach ($locationOverlay as $coordinate => $location) {
            $number = (int) $location['stock_location_ln'];
            if (isset($layouts[$number])) {
                continue;
            }
            $this->createLocation($connection, $tenantId, $warehouseId, $areaIds[strtoupper((string) $location['stock_location_zone'])], $aisleIds[$number], $location, $adminId, $date);
            ++$locationCount;
        }

        $this->createProducts($connection, $tenantId, $data['article'], $date);
        $this->createPartners($connection, $tenantId, $adminId, $data['customer'], $data['supplier'], $date);
        $this->createPurchaseOrders($connection, $tenantId, $adminId, $data, $date);
        $this->createOutboundOrders($connection, $tenantId, $adminId, $data, $date);
        $this->createStock($connection, $tenantId, $adminId, $data, $date);

        return $locationCount;
    }

    /** @return array<string, list<array<string, mixed>>> */
    private function data(): array
    {
        $contents = file_get_contents($this->projectDir . '/resources/installation/v2-demo-data.json');
        if (!is_string($contents)) {
            throw new RuntimeException('The normalized V2 demo dataset cannot be read.');
        }
        $data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new RuntimeException('The normalized V2 demo dataset is invalid.');
        }

        /** @var array<string, list<array<string, mixed>>> $data */
        return $data;
    }

    private function createArea(Connection $connection, string $tenantId, string $warehouseId, string $adminId, string $code, string $name, string $date): string
    {
        $id = $this->id($tenantId, 'area', $code);
        $connection->insert('wms_warehouse_area', [
            'id' => $id, 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId,
            'code' => $code, 'name' => mb_substr($name, 0, 100),
            'area_type' => $code === 'WAZ' ? 'shipping' : 'storage',
            'created_by' => $adminId, 'created_at' => $date,
        ]);

        return $id;
    }

    private function createAisle(Connection $connection, string $tenantId, string $areaId, string $adminId, int $number, string $name, string $model, string $date): string
    {
        $id = $this->id($tenantId, 'storage-structure', (string) $number);
        $connection->insert('wms_warehouse_aisle', [
            'id' => $id, 'tenant_id' => $tenantId, 'area_id' => $areaId,
            'code' => (string) $number, 'name' => mb_substr($name, 0, 100), 'storage_model' => mb_substr($model, 0, 20),
            'created_by' => $adminId, 'created_at' => $date,
        ]);

        return $id;
    }

    /**
     * @param array<string, array<string, mixed>> $overlay
     */
    private function createGrid(Connection $connection, string $tenantId, string $warehouseId, string $areaId, string $aisleId, int $number, int $levels, int $slots, int $depths, string $description, string $zone, array $overlay, string $adminId, string $date): int
    {
        $count = 0;
        for ($level = 1; $level <= $levels; ++$level) {
            for ($slot = 1; $slot <= $slots; ++$slot) {
                for ($depth = 1; $depth <= $depths; ++$depth) {
                    $coordinate = sprintf('%03d%04d%04d%04d', $number, $level, $slot, $depth);
                    $this->createLocation($connection, $tenantId, $warehouseId, $areaId, $aisleId, $overlay[$coordinate] ?? [
                        'stock_location_ln' => $number, 'stock_location_fb' => $level,
                        'stock_location_sp' => $slot, 'stock_location_tf' => $depth,
                        'stock_location_coordinate' => $coordinate, 'stock_location_desc' => $description,
                        'stock_location_width' => null, 'stock_location_depth' => null,
                        'stock_location_height' => null, 'stock_location_zone' => $zone,
                        'created_at' => $date,
                    ], $adminId, $date);
                    ++$count;
                }
            }
        }

        return $count;
    }

    /** @param array<string, mixed> $location */
    private function createLocation(Connection $connection, string $tenantId, string $warehouseId, string $areaId, string $aisleId, array $location, string $adminId, string $fallbackDate): void
    {
        $coordinate = (string) $location['stock_location_coordinate'];
        $level = (int) $location['stock_location_fb'];
        $slot = (int) $location['stock_location_sp'];
        $depth = (int) $location['stock_location_tf'];
        $zone = strtoupper((string) $location['stock_location_zone']);
        $connection->insert('wms_storage_location', [
            'id' => $this->id($tenantId, 'location', $coordinate), 'tenant_id' => $tenantId,
            'warehouse_id' => $warehouseId, 'area_id' => $areaId, 'aisle_id' => $aisleId,
            'code' => $coordinate, 'level_code' => (string) $level, 'bin_code' => sprintf('%04d-%04d', $slot, $depth),
            'location_type' => $zone, 'capacity_quantity' => 0,
            'warehouse_number' => (int) $location['stock_location_ln'], 'level_number' => $level,
            'slot_number' => $slot, 'depth_number' => $depth, 'coordinate' => $coordinate,
            'description' => (string) $location['stock_location_desc'],
            'width_mm' => $location['stock_location_width'], 'physical_depth_mm' => $location['stock_location_depth'],
            'height_mm' => $location['stock_location_height'], 'zone_code' => $zone,
            'putaway_enabled' => 1, 'putaway_priority' => 100, 'created_by' => $adminId,
            'created_at' => $location['created_at'] ?? $fallbackDate,
        ]);
    }

    /** @param list<array<string, mixed>> $articles */
    private function createProducts(Connection $connection, string $tenantId, array $articles, string $fallbackDate): void
    {
        foreach ($articles as $article) {
            $connection->insert('wms_product_reference', [
                'id' => $this->id($tenantId, 'product', (string) $article['article_id']),
                'tenant_id' => $tenantId, 'sku' => (string) $article['article_nr'], 'name' => (string) $article['article_name'],
                'gtin' => (string) $article['article_ean'], 'category' => (string) $article['article_category'],
                'base_unit' => strtoupper((string) $article['article_unit']), 'active' => 1,
                'weight_grams' => (int) $article['article_weight'], 'length_mm' => (int) $article['article_depth'],
                'width_mm' => (int) $article['article_width'], 'height_mm' => (int) $article['article_height'],
                'created_at' => $article['created_at'] ?? $fallbackDate,
            ]);
        }
    }

    /** @param list<array<string, mixed>> $customers @param list<array<string, mixed>> $suppliers */
    private function createPartners(Connection $connection, string $tenantId, string $adminId, array $customers, array $suppliers, string $date): void
    {
        foreach ($customers as $customer) {
            $connection->insert('wms_business_partner', [
                'id' => $this->id($tenantId, 'customer', (string) $customer['customer_id']), 'tenant_id' => $tenantId,
                'code' => (string) $customer['customer_nr'], 'name' => mb_substr((string) $customer['customer_name'], 0, 150),
                'partner_type' => 'customer', 'external_reference' => (string) $customer['customer_nr'], 'active' => 1,
                'created_by' => $adminId, 'created_at' => $customer['created_at'] ?? $date,
            ]);
        }
        foreach ($suppliers as $supplier) {
            $connection->insert('wms_supplier', [
                'id' => $this->id($tenantId, 'supplier', (string) $supplier['supplier_id']), 'tenant_id' => $tenantId,
                'code' => (string) $supplier['supplier_nr'], 'name' => mb_substr((string) $supplier['supplier_name'], 0, 100),
                'created_at' => $supplier['created_at'] ?? $date,
            ]);
        }
    }

    /** @param array<string, list<array<string, mixed>>> $data */
    private function createPurchaseOrders(Connection $connection, string $tenantId, string $adminId, array $data, string $date): void
    {
        $suppliers = [];
        foreach ($data['supplier'] as $supplier) {
            $suppliers[(int) $supplier['supplier_id']] = $supplier;
        }
        foreach ($data['supplier_orders'] as $order) {
            $supplier = $suppliers[(int) $order['supplier_id']];
            $orderId = $this->id($tenantId, 'purchase-order', (string) $order['supplier_order_id']);
            $connection->insert('wms_purchase_order', [
                'id' => $orderId, 'tenant_id' => $tenantId,
                'code' => mb_substr((string) $order['supplier_order_nr'], 0, 80),
                'supplier_reference' => mb_substr((string) ($order['supplier_order_reference'] ?? $order['supplier_id']), 0, 80),
                'status' => 'created', 'created_by' => $adminId,
                'created_at' => $order['created_at'] ?? $date, 'updated_at' => $order['updated_at'] ?? $order['created_at'] ?? $date,
            ]);
            $connection->insert('wms_inbound_delivery', [
                'id' => $this->id($tenantId, 'inbound-delivery', (string) $order['supplier_order_id']),
                'tenant_id' => $tenantId, 'purchase_order_id' => $orderId,
                'code' => mb_substr('IN-' . (string) $order['supplier_order_nr'], 0, 80),
                'delivery_note' => mb_substr('V2-' . (string) $order['supplier_order_id'], 0, 80),
                'expected_at' => $order['supplier_order_date'] ?? $order['created_at'] ?? $date, 'status' => 'planned',
                'sender_name' => mb_substr((string) $supplier['supplier_name'], 0, 160),
                'sender_street' => trim((string) $supplier['supplier_address_street'] . ' ' . (string) $supplier['supplier_address_street_nr']),
                'sender_postal_code' => (string) $supplier['supplier_address_zipcode'],
                'sender_city' => (string) $supplier['supplier_address_city'],
                'sender_country_code' => strtoupper((string) $supplier['supplier_address_country_code']),
                'created_by' => $adminId, 'created_at' => $order['created_at'] ?? $date,
                'updated_at' => $order['updated_at'] ?? $order['created_at'] ?? $date,
            ]);
        }
        foreach ($data['supplier_order_pos'] as $position) {
            $itemId = $this->id($tenantId, 'purchase-order-item', (string) $position['id']);
            $connection->insert('wms_purchase_order_item', [
                'id' => $itemId,
                'purchase_order_id' => $this->id($tenantId, 'purchase-order', (string) $position['supplier_order_id']),
                'product_id' => $this->id($tenantId, 'product', (string) $position['article_id']),
                'ordered_quantity' => (int) $position['supplier_order_pos_quantity'],
                'advised_quantity' => 0, 'received_quantity' => 0, 'status' => 'created',
            ]);
            $connection->insert('wms_inbound_delivery_line', [
                'id' => $this->id($tenantId, 'inbound-delivery-line', (string) $position['id']),
                'inbound_delivery_id' => $this->id($tenantId, 'inbound-delivery', (string) $position['supplier_order_id']),
                'purchase_order_item_id' => $itemId, 'advised_quantity' => (int) $position['supplier_order_pos_quantity'],
                'status' => 'planned',
            ]);
        }
    }

    /** @param array<string, list<array<string, mixed>>> $data */
    private function createOutboundOrders(Connection $connection, string $tenantId, string $adminId, array $data, string $date): void
    {
        $customers = [];
        foreach ($data['customer'] as $customer) {
            $customers[(int) $customer['customer_id']] = $customer;
        }
        foreach ($data['customer_orders'] as $order) {
            $customer = $customers[(int) $order['customer_id']];
            $connection->insert('wms_outbound_order', [
                'id' => $this->id($tenantId, 'outbound-order', (string) $order['customer_order_id']), 'tenant_id' => $tenantId,
                'order_number' => mb_substr((string) $order['customer_order_nr'], 0, 100),
                'customer_reference' => mb_substr((string) ($order['customer_order_reference'] ?? $customer['customer_nr']), 0, 100),
                'status' => 'created', 'recipient_name' => mb_substr((string) $customer['customer_name'], 0, 160),
                'recipient_street' => trim((string) $customer['customer_address_street'] . ' ' . (string) $customer['customer_address_street_nr']),
                'recipient_postal_code' => (string) $customer['customer_zip_code'], 'recipient_city' => (string) $customer['customer_city'],
                'recipient_country_code' => strtoupper((string) $customer['customer_country_code']),
                'created_by' => $adminId, 'created_at' => $order['created_at'] ?? $date,
            ]);
        }
        foreach ($data['customer_orders_pos'] as $position) {
            $connection->insert('wms_outbound_order_item', [
                'id' => $this->id($tenantId, 'outbound-order-item', (string) $position['id']),
                'outbound_order_id' => $this->id($tenantId, 'outbound-order', (string) $position['customer_order_id']),
                'product_id' => $this->id($tenantId, 'product', (string) $position['article_id']),
                'requested_quantity' => (int) $position['quantity'], 'reservation_id' => null,
            ]);
        }
    }

    /** @param array<string, list<array<string, mixed>>> $data */
    private function createStock(Connection $connection, string $tenantId, string $adminId, array $data, string $date): void
    {
        foreach ($data['stock_occupancy'] as $occupancy) {
            if ($occupancy['article_id'] === null || (float) ($occupancy['in_stock'] ?? 0) <= 0) {
                continue;
            }
            $productId = $this->id($tenantId, 'product', (string) $occupancy['article_id']);
            $locationId = $this->id($tenantId, 'location', (string) $occupancy['stock_coordinate']);
            $quantity = (int) $occupancy['in_stock'];
            $stockKey = hash('sha256', 'available|||');
            $connection->insert('wms_stock_balance', [
                'tenant_id' => $tenantId, 'product_id' => $productId, 'location_id' => $locationId,
                'stock_key' => $stockKey, 'stock_status' => 'available', 'quantity' => $quantity,
                'updated_at' => $occupancy['updated_at'] ?? $occupancy['created_at'] ?? $date,
            ]);
            $connection->insert('wms_stock_ledger', [
                'id' => $this->id($tenantId, 'opening-stock', (string) $occupancy['id']), 'tenant_id' => $tenantId,
                'product_id' => $productId, 'location_id' => $locationId, 'stock_key' => $stockKey,
                'stock_status' => 'available', 'quantity_delta' => $quantity, 'resulting_quantity' => $quantity,
                'movement_type' => 'posting', 'reason' => 'Übernommener V2-Demobestand',
                'performed_by' => $adminId, 'occurred_at' => $occupancy['last_incoming'] ?? $occupancy['created_at'] ?? $date,
            ]);
        }
    }

    private function id(string $tenantId, string $entity, string $legacyId): string
    {
        return Uuid::v5(Uuid::fromString($tenantId), sprintf('v2-demo:%s:%s', $entity, $legacyId))->toRfc4122();
    }
}
