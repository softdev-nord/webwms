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
}
