<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\StorageBinDefinition;

final readonly class WarehouseTopologyService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function createSite(string $id, string $tenantId, string $code, string $name, string $timezone, string $actorId, DateTimeImmutable $now): void
    {
        $code = $this->code($code, 20);
        $name = $this->name($name);
        $this->assertActor($tenantId, $actorId);
        if (!in_array($timezone, timezone_identifiers_list(), true)) {
            throw new \InvalidArgumentException('The site timezone is invalid.');
        }
        $this->connection->insert('wms_site', [
            'id' => $id, 'tenant_id' => $tenantId, 'code' => $code, 'name' => $name,
            'timezone' => $timezone, 'status' => 'active', 'created_by' => $actorId,
            'created_at' => $this->date($now), 'updated_at' => $this->date($now),
        ]);
    }

    public function createWarehouse(string $id, string $tenantId, string $siteId, string $code, string $name, string $type, string $actorId, DateTimeImmutable $now): void
    {
        $code = $this->code($code, 20);
        $name = $this->name($name);
        $type = $this->type($type, ['standard', 'high_bay', 'block', 'automated']);
        if (!$this->referenceExists('wms_site', $siteId, $tenantId) || !$this->referenceExists('wms_user_account', $actorId, $tenantId)) {
            throw new InventoryReferenceNotFoundException('Site and creator must exist in the tenant.');
        }
        $this->connection->insert('wms_warehouse', [
            'id' => $id, 'tenant_id' => $tenantId, 'site_id' => $siteId, 'code' => $code,
            'name' => $name, 'warehouse_type' => $type, 'created_by' => $actorId, 'created_at' => $this->date($now),
        ]);
    }

    public function createArea(string $id, string $tenantId, string $warehouseId, string $code, string $name, string $type, string $actorId, DateTimeImmutable $now): void
    {
        $code = $this->code($code, 30);
        $name = $this->name($name, 100);
        $type = $this->type($type, ['storage', 'receiving', 'shipping', 'quality', 'blocked']);
        if (!$this->referenceExists('wms_warehouse', $warehouseId, $tenantId) || !$this->referenceExists('wms_user_account', $actorId, $tenantId)) {
            throw new InventoryReferenceNotFoundException('Warehouse and creator must exist in the tenant.');
        }
        $this->connection->insert('wms_warehouse_area', [
            'id' => $id, 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId, 'code' => $code,
            'name' => $name, 'area_type' => $type, 'created_by' => $actorId, 'created_at' => $this->date($now),
        ]);
    }

    public function createAisle(string $id, string $tenantId, string $areaId, string $code, string $name, string $actorId, DateTimeImmutable $now): void
    {
        $code = $this->code($code, 30);
        $name = $this->name($name, 100);
        if (!$this->referenceExists('wms_warehouse_area', $areaId, $tenantId) || !$this->referenceExists('wms_user_account', $actorId, $tenantId)) {
            throw new InventoryReferenceNotFoundException('Area and creator must exist in the tenant.');
        }
        $this->connection->insert('wms_warehouse_aisle', [
            'id' => $id, 'tenant_id' => $tenantId, 'area_id' => $areaId, 'code' => $code,
            'name' => $name, 'created_by' => $actorId, 'created_at' => $this->date($now),
        ]);
    }

    public function createBin(string $id, string $tenantId, string $warehouseId, string $areaId, string $aisleId, string $code, string $levelCode, string $binCode, string $type, int $capacity, string $actorId, DateTimeImmutable $now): void
    {
        $bin = new StorageBinDefinition(
            mb_strtoupper(trim($code)),
            mb_strtoupper(trim($levelCode)),
            mb_strtoupper(trim($binCode)),
            $type,
            $capacity,
        );
        $validHierarchy = $this->connection->fetchOne(
            'SELECT 1 FROM wms_warehouse_aisle a INNER JOIN wms_warehouse_area ar ON ar.id = a.area_id '
            . 'WHERE a.id = :aisleId AND ar.id = :areaId AND ar.warehouse_id = :warehouseId '
            . 'AND a.tenant_id = :tenantId AND ar.tenant_id = :tenantId',
            ['aisleId' => $aisleId, 'areaId' => $areaId, 'warehouseId' => $warehouseId, 'tenantId' => $tenantId],
        ) !== false;
        if (!$validHierarchy || !$this->referenceExists('wms_user_account', $actorId, $tenantId)) {
            throw new InventoryReferenceNotFoundException('Warehouse, area, aisle and creator must belong to the tenant.');
        }
        $this->connection->insert('wms_storage_location', [
            'id' => $id, 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId,
            'area_id' => $areaId, 'aisle_id' => $aisleId, 'code' => $bin->code(),
            'level_code' => $bin->levelCode(), 'bin_code' => $bin->binCode(),
            'location_type' => $bin->locationType(), 'capacity_quantity' => $bin->capacityQuantity(),
            'putaway_enabled' => 1, 'putaway_priority' => 100,
            'created_by' => $actorId, 'created_at' => $this->date($now),
        ]);
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if (!$this->referenceExists('wms_user_account', $actorId, $tenantId)) {
            throw new InventoryReferenceNotFoundException('The creator must exist in the tenant.');
        }
    }

    private function referenceExists(string $table, string $id, string $tenantId): bool
    {
        return $this->connection->fetchOne(
            sprintf('SELECT 1 FROM %s WHERE id = :id AND tenant_id = :tenantId', $table),
            ['id' => $id, 'tenantId' => $tenantId],
        ) !== false;
    }

    private function code(string $code, int $maxLength): string
    {
        $code = mb_strtoupper(trim($code));
        if (preg_match('/^[A-Z0-9][A-Z0-9._-]+$/', $code) !== 1 || mb_strlen($code) > $maxLength) {
            throw new \InvalidArgumentException('The topology code is invalid.');
        }

        return $code;
    }

    private function name(string $name, int $maxLength = 255): string
    {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > $maxLength) {
            throw new \InvalidArgumentException('The topology name is invalid.');
        }

        return $name;
    }

    /** @param list<string> $supported */
    private function type(string $type, array $supported): string
    {
        if (!in_array($type, $supported, true)) {
            throw new \InvalidArgumentException('The topology type is not supported.');
        }

        return $type;
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
