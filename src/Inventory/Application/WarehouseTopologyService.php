<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
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

    /** @return array<string, mixed> */
    public function topologyEntry(string $tenantId, string $resource, string $id): array
    {
        $table = $this->table($resource);
        $entry = $this->connection->fetchAssociative(sprintf('SELECT * FROM %s WHERE id = :id AND tenant_id = :tenantId', $table), ['id' => $id, 'tenantId' => $tenantId]);
        if ($entry === false) {
            throw new InventoryReferenceNotFoundException('The topology entry does not exist in the tenant.');
        }

        return $entry;
    }

    /** @param array<string, mixed> $data */
    public function updateTopologyEntry(string $tenantId, string $actorId, string $resource, string $id, array $data, DateTimeImmutable $now): void
    {
        $this->assertActor($tenantId, $actorId);
        $before = $this->topologyEntry($tenantId, $resource, $id);
        $changes = match ($resource) {
            'site' => [
                'code' => $this->code($this->string($data, 'code'), 20),
                'name' => $this->name($this->string($data, 'name')),
                'timezone' => $this->timezone($this->string($data, 'timezone')),
                'status' => $this->type($this->string($data, 'status'), ['active', 'inactive']),
                'updated_at' => $this->date($now),
            ],
            'warehouse' => [
                'site_id' => $this->ownedReference('wms_site', $this->string($data, 'site_id'), $tenantId),
                'code' => $this->code($this->string($data, 'code'), 20),
                'name' => $this->name($this->string($data, 'name')),
                'warehouse_type' => $this->type($this->string($data, 'warehouse_type'), ['standard', 'high_bay', 'block', 'automated']),
            ],
            'area' => [
                'warehouse_id' => $this->ownedReference('wms_warehouse', $this->string($data, 'warehouse_id'), $tenantId),
                'code' => $this->code($this->string($data, 'code'), 30),
                'name' => $this->name($this->string($data, 'name'), 100),
                'area_type' => $this->type($this->string($data, 'area_type'), ['storage', 'receiving', 'shipping', 'quality', 'blocked']),
            ],
            'aisle' => [
                'area_id' => $this->ownedReference('wms_warehouse_area', $this->string($data, 'area_id'), $tenantId),
                'code' => $this->code($this->string($data, 'code'), 30),
                'name' => $this->name($this->string($data, 'name'), 100),
            ],
            'bin' => $this->binChanges($tenantId, $data),
            default => throw new \InvalidArgumentException('The topology resource is not supported.'),
        };

        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $resource, $id, $changes, $before, $now): void {
            $connection->update($this->table($resource), $changes, ['id' => $id, 'tenant_id' => $tenantId]);
            $connection->insert('wms_administration_event', [
                'id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId,
                'aggregate_type' => 'warehouse_' . $resource, 'aggregate_id' => $id,
                'event_type' => 'updated', 'payload' => json_encode(['before' => $before, 'after' => $changes], JSON_THROW_ON_ERROR),
                'performed_by' => $actorId, 'occurred_at' => $this->date($now),
            ]);
        });
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function binChanges(string $tenantId, array $data): array
    {
        $warehouseId = $this->ownedReference('wms_warehouse', $this->string($data, 'warehouse_id'), $tenantId);
        $areaId = $this->ownedReference('wms_warehouse_area', $this->string($data, 'area_id'), $tenantId);
        $aisleId = $this->ownedReference('wms_warehouse_aisle', $this->string($data, 'aisle_id'), $tenantId);
        if ($this->connection->fetchOne('SELECT 1 FROM wms_warehouse_aisle a JOIN wms_warehouse_area ar ON ar.id = a.area_id WHERE a.id = :aisle AND ar.id = :area AND ar.warehouse_id = :warehouse AND a.tenant_id = :tenant', ['aisle' => $aisleId, 'area' => $areaId, 'warehouse' => $warehouseId, 'tenant' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('The bin hierarchy is invalid.');
        }
        $bin = new StorageBinDefinition($this->string($data, 'code'), $this->string($data, 'level_code'), $this->string($data, 'bin_code'), $this->string($data, 'location_type'), $this->integer($data, 'capacity_quantity'));

        return ['warehouse_id' => $warehouseId, 'area_id' => $areaId, 'aisle_id' => $aisleId, 'code' => $bin->code(), 'level_code' => $bin->levelCode(), 'bin_code' => $bin->binCode(), 'location_type' => $bin->locationType(), 'capacity_quantity' => $bin->capacityQuantity(), 'putaway_enabled' => $this->boolean($data, 'putaway_enabled') ? 1 : 0, 'putaway_priority' => $this->integer($data, 'putaway_priority')];
    }

    private function table(string $resource): string
    {
        return match ($resource) {
            'site' => 'wms_site', 'warehouse' => 'wms_warehouse', 'area' => 'wms_warehouse_area',
            'aisle' => 'wms_warehouse_aisle', 'bin' => 'wms_storage_location',
            default => throw new \InvalidArgumentException('The topology resource is not supported.'),
        };
    }

    private function ownedReference(string $table, string $id, string $tenantId): string
    {
        if (!$this->referenceExists($table, $id, $tenantId)) {
            throw new InventoryReferenceNotFoundException('The referenced topology entry does not exist in the tenant.');
        }

        return $id;
    }

    /** @param array<string, mixed> $data */
    private function string(array $data, string $field): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $data */
    private function integer(array $data, string $field): int
    {
        $value = $data[$field] ?? null;
        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function boolean(array $data, string $field): bool
    {
        return filter_var($data[$field] ?? false, FILTER_VALIDATE_BOOL);
    }

    private function timezone(string $timezone): string
    {
        if (!in_array($timezone, timezone_identifiers_list(), true)) {
            throw new \InvalidArgumentException('The site timezone is invalid.');
        }

        return $timezone;
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
