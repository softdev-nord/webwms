<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class InventoryControlService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function workspace(string $tenantId): array
    {
        return [
            'products' => $this->rows('SELECT id, sku, name FROM wms_product_reference WHERE tenant_id = :tenantId ORDER BY sku', $tenantId),
            'warehouses' => $this->rows('SELECT id, code, name FROM wms_warehouse WHERE tenant_id = :tenantId ORDER BY code', $tenantId),
            'hazardClasses' => $this->rows('SELECT * FROM wms_hazard_class WHERE tenant_id = :tenantId ORDER BY code', $tenantId),
            'hazardousMaterials' => $this->rows('SELECT hm.*, p.sku, hc.code AS hazard_code FROM wms_hazardous_material hm JOIN wms_product_reference p ON p.id = hm.product_id JOIN wms_hazard_class hc ON hc.id = hm.hazard_class_id WHERE hm.tenant_id = :tenantId ORDER BY p.sku', $tenantId),
            'restrictions' => $this->rows('SELECT r.*, hc.code AS hazard_code FROM wms_storage_restriction r JOIN wms_hazard_class hc ON hc.id = r.hazard_class_id WHERE r.tenant_id = :tenantId ORDER BY hc.code, r.location_prefix', $tenantId),
            'boms' => $this->rows('SELECT b.*, p.sku, COUNT(i.id) AS item_count FROM wms_bill_of_material b JOIN wms_product_reference p ON p.id = b.product_id LEFT JOIN wms_bom_item i ON i.bill_of_material_id = b.id WHERE b.tenant_id = :tenantId GROUP BY b.id, p.sku ORDER BY b.code, b.version', $tenantId),
            'requirements' => $this->rows('SELECT r.*, b.code AS bom_code FROM wms_material_requirement r JOIN wms_bill_of_material b ON b.id = r.bill_of_material_id WHERE r.tenant_id = :tenantId ORDER BY r.created_at DESC', $tenantId),
            'carrierAccounts' => $this->rows('SELECT * FROM wms_load_carrier_account WHERE tenant_id = :tenantId ORDER BY partner_code, carrier_type', $tenantId),
            'carrierMovements' => $this->rows('SELECT m.*, a.partner_code, a.carrier_type FROM wms_load_carrier_movement m JOIN wms_load_carrier_account a ON a.id = m.account_id WHERE m.tenant_id = :tenantId ORDER BY m.booked_at DESC LIMIT 100', $tenantId),
            'cyclePlans' => $this->rows('SELECT p.*, w.code AS warehouse_code FROM wms_cycle_count_plan p JOIN wms_warehouse w ON w.id = p.warehouse_id WHERE p.tenant_id = :tenantId ORDER BY p.next_due_at', $tenantId),
            'counts' => $this->rows('SELECT c.*, w.code AS warehouse_code FROM wms_inventory_count c JOIN wms_warehouse w ON w.id = c.warehouse_id WHERE c.tenant_id = :tenantId ORDER BY c.created_at DESC LIMIT 100', $tenantId),
            'countLines' => $this->rows('SELECT l.*, c.code AS count_code, p.sku FROM wms_inventory_count_line l JOIN wms_inventory_count c ON c.id = l.inventory_count_id JOIN wms_product_reference p ON p.id = l.product_id WHERE c.tenant_id = :tenantId AND c.status IN (\'open\', \'counted\', \'pending_approval\') ORDER BY c.created_at DESC, p.sku LIMIT 250', $tenantId),
        ];
    }

    public function createHazardClass(string $tenantId, string $actorId, string $code, string $name, string $unClass, DateTimeImmutable $now): string
    {
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_hazard_class', ['id' => $id, 'tenant_id' => $tenantId, 'code' => $this->required($code), 'name' => $this->required($name), 'un_class' => $this->required($unClass), 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    public function classifyMaterial(string $tenantId, string $actorId, string $productId, string $hazardClassId, string $unNumber, ?string $packingGroup, string $description, DateTimeImmutable $now): string
    {
        return $this->connection->transactional(function () use ($tenantId, $actorId, $productId, $hazardClassId, $unNumber, $packingGroup, $description, $now): string {
            $this->assertOwned('wms_product_reference', $productId, $tenantId);
            $this->assertOwned('wms_hazard_class', $hazardClassId, $tenantId);
            $id = Uuid::v7()->toRfc4122();
            $this->connection->insert('wms_hazardous_material', ['id' => $id, 'tenant_id' => $tenantId, 'product_id' => $productId, 'hazard_class_id' => $hazardClassId, 'un_number' => $this->required($unNumber), 'packing_group' => $this->nullable($packingGroup), 'description' => $this->required($description), 'created_by' => $actorId, 'created_at' => $this->date($now)]);

            return $id;
        });
    }

    public function restrictStorage(string $tenantId, string $actorId, string $hazardClassId, string $locationPrefix, bool $allowed, ?int $maxQuantity, DateTimeImmutable $now): string
    {
        if ($maxQuantity !== null && $maxQuantity < 0) {
            throw new \InvalidArgumentException('Die Höchstmenge darf nicht negativ sein.');
        }
        $this->assertOwned('wms_hazard_class', $hazardClassId, $tenantId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_storage_restriction', ['id' => $id, 'tenant_id' => $tenantId, 'hazard_class_id' => $hazardClassId, 'location_prefix' => $this->required($locationPrefix), 'allowed' => $allowed ? 1 : 0, 'max_quantity' => $maxQuantity, 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    /** @param list<array{productId: string, quantity: int}> $items */
    public function createBom(string $tenantId, string $actorId, string $productId, string $code, string $version, array $items, DateTimeImmutable $now): string
    {
        if ($items === []) {
            throw new \InvalidArgumentException('Eine Stückliste benötigt mindestens eine Komponente.');
        }

        return $this->connection->transactional(function () use ($tenantId, $actorId, $productId, $code, $version, $items, $now): string {
            $this->assertOwned('wms_product_reference', $productId, $tenantId);
            $id = Uuid::v7()->toRfc4122();
            $this->connection->insert('wms_bill_of_material', ['id' => $id, 'tenant_id' => $tenantId, 'product_id' => $productId, 'code' => $this->required($code), 'version' => $this->required($version), 'active' => 1, 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            foreach ($items as $position => $item) {
                if ($item['quantity'] <= 0) {
                    throw new \InvalidArgumentException('Komponentenmengen müssen positiv sein.');
                }
                $this->assertOwned('wms_product_reference', $item['productId'], $tenantId);
                $this->connection->insert('wms_bom_item', ['id' => Uuid::v7()->toRfc4122(), 'bill_of_material_id' => $id, 'component_product_id' => $item['productId'], 'quantity' => $item['quantity'], 'position' => $position + 1]);
            }

            return $id;
        });
    }

    public function planRequirement(string $tenantId, string $actorId, string $bomId, string $reference, int $productionQuantity, DateTimeImmutable $now): string
    {
        if ($productionQuantity <= 0) {
            throw new \InvalidArgumentException('Die Produktionsmenge muss positiv sein.');
        }
        $this->assertOwned('wms_bill_of_material', $bomId, $tenantId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_material_requirement', ['id' => $id, 'tenant_id' => $tenantId, 'bill_of_material_id' => $bomId, 'reference' => $this->required($reference), 'production_quantity' => $productionQuantity, 'status' => 'planned', 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    public function bookLoadCarrier(string $tenantId, string $actorId, string $partnerCode, string $carrierType, int $quantity, string $reference, string $note, DateTimeImmutable $now): string
    {
        if ($quantity === 0) {
            throw new \InvalidArgumentException('Die Buchungsmenge darf nicht null sein.');
        }

        return $this->connection->transactional(function () use ($tenantId, $actorId, $partnerCode, $carrierType, $quantity, $reference, $note, $now): string {
            $accountId = $this->connection->fetchOne('SELECT id FROM wms_load_carrier_account WHERE tenant_id = :tenantId AND partner_code = :partnerCode AND carrier_type = :carrierType FOR UPDATE', ['tenantId' => $tenantId, 'partnerCode' => $this->required($partnerCode), 'carrierType' => $this->required($carrierType)]);
            if (!is_string($accountId)) {
                $accountId = Uuid::v7()->toRfc4122();
                $this->connection->insert('wms_load_carrier_account', ['id' => $accountId, 'tenant_id' => $tenantId, 'partner_code' => $partnerCode, 'carrier_type' => $carrierType, 'balance' => 0, 'created_at' => $this->date($now), 'updated_at' => $this->date($now)]);
            }
            $this->connection->executeStatement('UPDATE wms_load_carrier_account SET balance = balance + :quantity, updated_at = :updatedAt WHERE id = :id AND tenant_id = :tenantId', ['quantity' => $quantity, 'updatedAt' => $this->date($now), 'id' => $accountId, 'tenantId' => $tenantId]);
            $id = Uuid::v7()->toRfc4122();
            $this->connection->insert('wms_load_carrier_movement', ['id' => $id, 'account_id' => $accountId, 'tenant_id' => $tenantId, 'quantity' => $quantity, 'reference' => $this->required($reference), 'note' => trim($note), 'booked_by' => $actorId, 'booked_at' => $this->date($now)]);

            return $id;
        });
    }

    /** @return array<string, string> */
    public function differenceLedgerIds(string $tenantId, string $countId): array
    {
        $lines = $this->connection->fetchFirstColumn('SELECT l.id FROM wms_inventory_count_line l JOIN wms_inventory_count c ON c.id = l.inventory_count_id WHERE c.id = :countId AND c.tenant_id = :tenantId AND l.difference_quantity <> 0 ORDER BY l.id', ['countId' => $countId, 'tenantId' => $tenantId]);
        $ids = [];
        foreach ($lines as $lineId) {
            if (is_string($lineId)) {
                $ids[$lineId] = Uuid::v7()->toRfc4122();
            }
        }

        return $ids;
    }

    /** @return list<array<string, mixed>> */
    private function rows(string $sql, string $tenantId): array
    {
        return $this->connection->fetchAllAssociative($sql, ['tenantId' => $tenantId]);
    }

    private function assertOwned(string $table, string $id, string $tenantId): void
    {
        if ($this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id AND tenant_id = :tenantId', $table), ['id' => $id, 'tenantId' => $tenantId]) === false) {
            throw new \DomainException('Die referenzierte Ressource gehört nicht zum Mandanten.');
        }
    }

    private function required(string $value): string
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('Ein Pflichtwert fehlt.');
        }

        return trim($value);
    }

    private function nullable(?string $value): ?string
    {
        return $value === null || trim($value) === '' ? null : trim($value);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }

}
