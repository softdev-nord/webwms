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
    }

    private function id(string $tenantId, string $name): string
    {
        return Uuid::v5(Uuid::fromString($tenantId), 'v3-demo:' . $name)->toRfc4122();
    }
}
