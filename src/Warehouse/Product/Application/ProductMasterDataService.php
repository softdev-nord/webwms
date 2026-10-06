<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Product\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;

readonly class ProductMasterDataService
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function products(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT p.*, COALESCE(SUM(b.quantity), 0) stock_quantity, COUNT(DISTINCT b.location_id) location_count '
            . 'FROM wms_product_reference p LEFT JOIN wms_stock_balance b ON b.product_id = p.id AND b.tenant_id = p.tenant_id '
            . 'WHERE p.tenant_id = :tenantId GROUP BY p.id ORDER BY p.sku',
            ['tenantId' => $tenantId],
        );
    }

    /** @return array<string, mixed> */
    public function product(string $tenantId, string $productId): array
    {
        $product = $this->connection->fetchAssociative(
            'SELECT * FROM wms_product_reference WHERE id = :id AND tenant_id = :tenantId',
            ['id' => $productId, 'tenantId' => $tenantId],
        );
        if ($product === false) {
            throw new InventoryReferenceNotFoundException('The product does not exist in the tenant.');
        }

        return $product;
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function context(string $tenantId, string $productId): array
    {
        $parameters = ['tenantId' => $tenantId, 'productId' => $productId];

        return [
            'stock' => $this->connection->fetchAllAssociative(
                'SELECT l.code location_code, w.code warehouse_code, b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity, b.updated_at '
                . 'FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id INNER JOIN wms_warehouse w ON w.id = l.warehouse_id '
                . 'WHERE b.tenant_id = :tenantId AND b.product_id = :productId ORDER BY w.code, l.code',
                $parameters,
            ),
            'movements' => $this->connection->fetchAllAssociative(
                'SELECT e.movement_type, e.quantity_delta, e.resulting_quantity, e.stock_status, e.reason, e.occurred_at, l.code location_code '
                . 'FROM wms_stock_ledger e INNER JOIN wms_storage_location l ON l.id = e.location_id '
                . 'WHERE e.tenant_id = :tenantId AND e.product_id = :productId ORDER BY e.occurred_at DESC LIMIT 100',
                $parameters,
            ),
            'inbound' => $this->connection->fetchAllAssociative(
                'SELECT o.code, o.status, o.created_at, i.ordered_quantity, i.advised_quantity, i.received_quantity '
                . 'FROM wms_purchase_order_item i INNER JOIN wms_purchase_order o ON o.id = i.purchase_order_id '
                . 'WHERE o.tenant_id = :tenantId AND i.product_id = :productId ORDER BY o.created_at DESC LIMIT 100',
                $parameters,
            ),
            'outbound' => $this->connection->fetchAllAssociative(
                'SELECT o.order_number code, o.status, o.created_at, i.requested_quantity '
                . 'FROM wms_outbound_order_item i INNER JOIN wms_outbound_order o ON o.id = i.outbound_order_id '
                . 'WHERE o.tenant_id = :tenantId AND i.product_id = :productId ORDER BY o.created_at DESC LIMIT 100',
                $parameters,
            ),
            'boms' => $this->connection->fetchAllAssociative(
                'SELECT b.id, b.code, b.version, b.active, COUNT(i.id) item_count FROM wms_bill_of_material b '
                . 'LEFT JOIN wms_bom_item i ON i.bill_of_material_id = b.id WHERE b.tenant_id = :tenantId AND b.product_id = :productId '
                . 'GROUP BY b.id, b.code, b.version, b.active ORDER BY b.code, b.version',
                $parameters,
            ),
            'hazards' => $this->connection->fetchAllAssociative(
                'SELECT hm.un_number, hm.packing_group, hm.description, hc.code hazard_class, hc.name hazard_class_name '
                . 'FROM wms_hazardous_material hm INNER JOIN wms_hazard_class hc ON hc.id = hm.hazard_class_id '
                . 'WHERE hm.tenant_id = :tenantId AND hm.product_id = :productId',
                $parameters,
            ),
            'history' => $this->connection->fetchAllAssociative(
                "SELECT e.event_type, e.payload, e.occurred_at, u.display_name performed_by_name FROM wms_administration_event e "
                . 'INNER JOIN wms_user_account u ON u.id = e.performed_by WHERE e.tenant_id = :tenantId '
                . "AND e.aggregate_type = 'product' AND e.aggregate_id = :productId ORDER BY e.occurred_at DESC LIMIT 100",
                $parameters,
            ),
        ];
    }

    /** @param array<string, mixed> $input */
    public function create(string $id, string $tenantId, array $input, string $actorId, DateTimeImmutable $now): void
    {
        $values = $this->values($input);
        $this->assertActor($tenantId, $actorId);
        $this->connection->transactional(function (Connection $connection) use ($id, $tenantId, $values, $actorId, $now): void {
            $connection->insert('wms_product_reference', [
                'id' => $id,
                'tenant_id' => $tenantId,
                ...$values,
                'created_at' => $this->date($now),
            ]);
            $this->event($connection, $tenantId, $id, 'created', ['after' => $values], $actorId, $now);
        });
    }

    /** @param array<string, mixed> $input */
    public function update(string $tenantId, string $productId, array $input, string $actorId, DateTimeImmutable $now): void
    {
        $values = [...$this->values($input), 'updated_at' => $this->date($now)];
        $this->assertActor($tenantId, $actorId);
        $before = $this->product($tenantId, $productId);
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $productId, $values, $actorId, $now, $before): void {
            $connection->update('wms_product_reference', $values, ['id' => $productId, 'tenant_id' => $tenantId]);
            $this->event($connection, $tenantId, $productId, 'updated', ['before' => $before, 'after' => $values], $actorId, $now);
        });
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    private function values(array $input): array
    {
        $sku = $this->required($input, 'sku', 64);
        $name = $this->required($input, 'name', 255);
        $gtin = $this->optional($input, 'gtin', 14);
        if ($gtin !== null && (!ctype_digit($gtin) || !in_array(strlen($gtin), [8, 12, 13, 14], true))) {
            throw new InvalidArgumentException('A GTIN must contain 8, 12, 13 or 14 digits.');
        }
        $abc = $this->optional($input, 'abc_classification', 1);
        if ($abc !== null && !in_array($abc, ['A', 'B', 'C', 'D', 'E', 'F'], true)) {
            throw new InvalidArgumentException('The ABC classification is invalid.');
        }

        return [
            'sku' => $sku, 'name' => $name,
            'description' => $this->optional($input, 'description'), 'description_en' => $this->optional($input, 'description_en'),
            'short_description' => $this->optional($input, 'short_description', 255), 'gtin' => $gtin,
            'category' => $this->optional($input, 'category', 100), 'base_unit' => strtoupper($this->required($input, 'base_unit', 20)),
            'abc_classification' => $abc, 'crash_class' => $this->integer($input, 'crash_class', 0, 11),
            'bulk_material' => $this->boolean($input, 'bulk_material'), 'hazardous_material' => $this->boolean($input, 'hazardous_material'),
            'serial_number_required' => $this->triState($input, 'serial_number_required'), 'batch_required' => $this->triState($input, 'batch_required'),
            'expiry_required' => $this->triState($input, 'expiry_required'), 'shelf_life_days' => $this->integer($input, 'shelf_life_days', 0),
            'expiry_warning_days' => $this->integer($input, 'expiry_warning_days', 0), 'max_expiry_mix_days' => $this->integer($input, 'max_expiry_mix_days', 0),
            'weight_grams' => $this->integer($input, 'weight_grams', 0), 'net_weight_grams' => $this->integer($input, 'net_weight_grams', 0),
            'length_mm' => $this->integer($input, 'length_mm', 0), 'width_mm' => $this->integer($input, 'width_mm', 0),
            'height_mm' => $this->integer($input, 'height_mm', 0), 'volume_cm3' => $this->integer($input, 'volume_cm3', 0),
            'nesting_factor' => $this->decimal($input, 'nesting_factor', 0, 1), 'goods_value' => $this->decimal($input, 'goods_value', 0),
            'goods_value_currency' => $this->optionalUpper($input, 'goods_value_currency', 3),
            'customs_tariff_number' => $this->optional($input, 'customs_tariff_number', 30),
            'pharmaceutical_number' => $this->optional($input, 'pharmaceutical_number', 30),
            'customer_material_number' => $this->optional($input, 'customer_material_number', 80),
            'supplier_material_number' => $this->optional($input, 'supplier_material_number', 80),
            'product_area_code' => $this->optional($input, 'product_area_code', 40),
            'inbound_note' => $this->optional($input, 'inbound_note'), 'picking_note' => $this->optional($input, 'picking_note'),
            'transport_note' => $this->optional($input, 'transport_note'), 'packing_note' => $this->optional($input, 'packing_note'),
            'loading_note' => $this->optional($input, 'loading_note'), 'overdelivery_percent' => $this->decimal($input, 'overdelivery_percent', 0),
            'active' => $this->boolean($input, 'active'),
        ];
    }

    /** @param array<string, mixed> $input */
    private function required(array $input, string $key, int $maximum): string
    {
        $value = trim((string) ($input[$key] ?? ''));
        if ($value === '' || mb_strlen($value) > $maximum) {
            throw new InvalidArgumentException(sprintf('The field "%s" is required and may contain up to %d characters.', $key, $maximum));
        }

        return $value;
    }

    /** @param array<string, mixed> $input */
    private function optional(array $input, string $key, ?int $maximum = null): ?string
    {
        $value = trim((string) ($input[$key] ?? ''));
        if ($value === '') {
            return null;
        }
        if ($maximum !== null && mb_strlen($value) > $maximum) {
            throw new InvalidArgumentException(sprintf('The field "%s" is too long.', $key));
        }

        return $value;
    }

    /** @param array<string, mixed> $input */
    private function optionalUpper(array $input, string $key, int $maximum): ?string
    {
        $value = $this->optional($input, $key, $maximum);

        return $value === null ? null : strtoupper($value);
    }

    /** @param array<string, mixed> $input */
    private function integer(array $input, string $key, int $minimum, ?int $maximum = null): ?int
    {
        $value = trim((string) ($input[$key] ?? ''));
        if ($value === '') {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < $minimum || ($maximum !== null && (int) $value > $maximum)) {
            throw new InvalidArgumentException(sprintf('The field "%s" contains an invalid number.', $key));
        }

        return (int) $value;
    }

    /** @param array<string, mixed> $input */
    private function decimal(array $input, string $key, float $minimum, ?float $maximum = null): ?string
    {
        $value = str_replace(',', '.', trim((string) ($input[$key] ?? '')));
        if ($value === '') {
            return null;
        }
        if (!is_numeric($value) || (float) $value < $minimum || ($maximum !== null && (float) $value > $maximum)) {
            throw new InvalidArgumentException(sprintf('The field "%s" contains an invalid decimal number.', $key));
        }

        return $value;
    }

    /** @param array<string, mixed> $input */
    private function boolean(array $input, string $key): int
    {
        return filter_var($input[$key] ?? false, FILTER_VALIDATE_BOOL) ? 1 : 0;
    }

    /** @param array<string, mixed> $input */
    private function triState(array $input, string $key): ?int
    {
        $value = (string) ($input[$key] ?? '');

        return $value === '' ? null : ($value === '1' ? 1 : 0);
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId', ['id' => $actorId, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('The user must exist in the tenant.');
        }
    }

    /** @param array<string, mixed> $payload */
    private function event(Connection $connection, string $tenantId, string $productId, string $type, array $payload, string $actorId, DateTimeImmutable $now): void
    {
        $connection->insert('wms_administration_event', [
            'id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => 'product',
            'aggregate_id' => $productId, 'event_type' => $type, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'performed_by' => $actorId, 'occurred_at' => $this->date($now),
        ]);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
