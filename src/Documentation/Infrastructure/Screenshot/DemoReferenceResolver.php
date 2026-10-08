<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Infrastructure\Screenshot;

use Doctrine\DBAL\Connection;
use RuntimeException;
use WebWMS\Documentation\Application\Screenshot\DemoReferenceProvider;

final readonly class DemoReferenceResolver implements DemoReferenceProvider
{
    public function __construct(private Connection $connection)
    {
    }

    /** @param array<string, string> $values @return array<string, string> */
    public function resolve(array $values): array
    {
        foreach ($values as $key => $value) {
            if (!str_starts_with($value, '@demo.')) {
                continue;
            }
            $values[$key] = $this->reference($value);
        }

        return $values;
    }

    private function reference(string $reference): string
    {
        [$sql, $parameters] = match ($reference) {
            '@demo.product.default' => ['SELECT id FROM wms_product_reference WHERE active = 1 ORDER BY sku LIMIT 1', []],
            '@demo.user.default' => ["SELECT id FROM wms_user_account WHERE status = 'active' ORDER BY email LIMIT 1", []],
            '@demo.role.default' => ['SELECT id FROM wms_role ORDER BY name LIMIT 1', []],
            '@demo.api_client.default' => ['SELECT id FROM wms_api_client ORDER BY name LIMIT 1', []],
            '@demo.stock.product' => [$this->stockSql('product_id'), []],
            '@demo.stock.location' => [$this->stockSql('location_id'), []],
            '@demo.stock.key' => [$this->stockSql('stock_key'), []],
            '@demo.stock_block.default' => ['SELECT id FROM wms_stock_block ORDER BY blocked_at DESC LIMIT 1', []],
            '@demo.unplanned_receipt.default' => ['SELECT id FROM wms_unplanned_receipt ORDER BY accepted_at DESC LIMIT 1', []],
            '@demo.outbound_order.default' => ['SELECT id FROM wms_outbound_order ORDER BY created_at DESC LIMIT 1', []],
            '@demo.pick_list.default' => ['SELECT id FROM wms_pick_list ORDER BY created_at DESC LIMIT 1', []],
            '@demo.packing_order.default' => ['SELECT id FROM wms_packing_order ORDER BY created_at DESC LIMIT 1', []],
            '@demo.shipment.default' => ['SELECT id FROM wms_shipment ORDER BY created_at DESC LIMIT 1', []],
            '@demo.loading_manifest.default' => ['SELECT id FROM wms_loading_manifest ORDER BY created_at DESC LIMIT 1', []],
            '@demo.erp_connection.default' => ['SELECT id FROM wms_erp_connection ORDER BY name LIMIT 1', []],
            '@demo.carrier_connection.default' => ['SELECT id FROM wms_carrier_connection ORDER BY name LIMIT 1', []],
            '@demo.device.default' => ['SELECT id FROM wms_device ORDER BY name LIMIT 1', []],
            '@demo.scan_event.default' => ['SELECT id FROM wms_scan_event ORDER BY scanned_at DESC LIMIT 1', []],
            '@demo.measurement.default' => ['SELECT id FROM wms_measurement ORDER BY measured_at DESC LIMIT 1', []],
            '@demo.printer.default' => ['SELECT id FROM wms_printer ORDER BY name LIMIT 1', []],
            '@demo.print_job.default' => ['SELECT id FROM wms_print_job ORDER BY created_at DESC LIMIT 1', []],
            '@demo.machine_command.default' => ['SELECT id FROM wms_machine_command ORDER BY created_at DESC LIMIT 1', []],
            '@demo.extension_work_item.default' => ['SELECT id FROM wms_extension_work_item ORDER BY changed_at DESC LIMIT 1', []],
            '@demo.outbox.default' => ['SELECT id FROM wms_integration_outbox ORDER BY occurred_at DESC LIMIT 1', []],
            '@demo.automation_command.default' => ['SELECT id FROM wms_device_command ORDER BY created_at DESC LIMIT 1', []],
            '@demo.transport_endpoint.default' => ['SELECT id FROM wms_transport_endpoint ORDER BY name LIMIT 1', []],
            '@demo.warehouse.default' => ['SELECT id FROM wms_warehouse ORDER BY code LIMIT 1', []],
            '@demo.warehouse.block' => [$this->warehouseSql(), ['model1' => '%BLOCK%', 'model2' => '%BLL%', 'model3' => '%BLOCK%']],
            '@demo.warehouse.rack' => [$this->warehouseSql(), ['model1' => '%RACK%', 'model2' => '%HRL%', 'model3' => '%FBL%']],
            '@demo.warehouse.flow' => [$this->warehouseSql(), ['model1' => '%FLOW%', 'model2' => '%RGL%', 'model3' => '%DLK%']],
            '@demo.aisle.block' => [$this->aisleSql(), ['model1' => '%BLOCK%', 'model2' => '%BLL%', 'model3' => '%BLOCK%']],
            '@demo.aisle.rack' => [$this->aisleSql(), ['model1' => '%RACK%', 'model2' => '%HRL%', 'model3' => '%FBL%']],
            '@demo.aisle.flow' => [$this->aisleSql(), ['model1' => '%FLOW%', 'model2' => '%RGL%', 'model3' => '%DLK%']],
            default => throw new RuntimeException(sprintf('Unknown handbook demo reference "%s".', $reference)),
        };
        $value = $this->connection->fetchOne($sql, $parameters);
        if (!is_string($value) || $value === '') {
            throw new RuntimeException(sprintf('No demo data matches handbook reference "%s".', $reference));
        }

        return $value;
    }

    private function aisleSql(): string
    {
        return 'SELECT DISTINCT a.id FROM wms_warehouse_aisle a '
            . 'INNER JOIN wms_storage_location l ON l.aisle_id = a.id '
            . 'WHERE UPPER(a.storage_model) LIKE :model1 OR UPPER(l.zone_code) LIKE :model1 '
            . 'OR UPPER(a.storage_model) LIKE :model2 OR UPPER(l.zone_code) LIKE :model2 '
            . 'OR UPPER(a.storage_model) LIKE :model3 OR UPPER(l.zone_code) LIKE :model3 '
            . 'ORDER BY a.code, a.id LIMIT 1';
    }

    private function warehouseSql(): string
    {
        return 'SELECT DISTINCT l.warehouse_id FROM wms_warehouse_aisle a '
            . 'INNER JOIN wms_storage_location l ON l.aisle_id = a.id '
            . 'WHERE UPPER(a.storage_model) LIKE :model1 OR UPPER(l.zone_code) LIKE :model1 '
            . 'OR UPPER(a.storage_model) LIKE :model2 OR UPPER(l.zone_code) LIKE :model2 '
            . 'OR UPPER(a.storage_model) LIKE :model3 OR UPPER(l.zone_code) LIKE :model3 '
            . 'ORDER BY a.code, a.id LIMIT 1';
    }

    private function stockSql(string $column): string
    {
        return sprintf(
            'SELECT %s FROM wms_stock_balance WHERE quantity > 0 ORDER BY product_id, location_id, stock_key LIMIT 1',
            $column,
        );
    }
}
