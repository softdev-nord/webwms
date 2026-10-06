<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Topology\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Warehouse\Topology\Domain\StorageBinDefinition;
use WebWMS\Warehouse\Topology\Domain\StorageLocationCoordinate;

readonly class WarehouseTopologyService
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
            throw new InvalidArgumentException('The site timezone is invalid.');
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

    public function createBin(string $id, string $tenantId, string $warehouseId, string $areaId, string $aisleId, string $code, string $levelCode, string $binCode, string $type, int $capacity, string $actorId, DateTimeImmutable $now, ?int $warehouseNumber = null, ?int $levelNumber = null, ?int $slotNumber = null, ?int $depthNumber = null, ?string $description = null, ?string $zoneCode = null, ?float $widthMm = null, ?float $physicalDepthMm = null, ?float $heightMm = null): void
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
            'warehouse_number' => $warehouseNumber, 'level_number' => $levelNumber,
            'slot_number' => $slotNumber, 'depth_number' => $depthNumber,
            'coordinate' => $warehouseNumber === null || $levelNumber === null || $slotNumber === null || $depthNumber === null
                ? null : $this->coordinate($warehouseNumber, $levelNumber, $slotNumber, $depthNumber),
            'description' => $description === null ? null : $this->name($description),
            'zone_code' => $zoneCode === null ? null : $this->code($zoneCode, 30),
            'width_mm' => $this->dimension($widthMm), 'physical_depth_mm' => $this->dimension($physicalDepthMm),
            'height_mm' => $this->dimension($heightMm),
            'putaway_enabled' => 1, 'putaway_priority' => 100,
            'created_by' => $actorId, 'created_at' => $this->date($now),
        ]);
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public function generateGrid(string $tenantId, string $warehouseId, string $areaId, string $aisleId, int $warehouseNumber, int $levels, int $slots, int $depths, string $description, string $zoneCode, string $locationType, float $widthMm, float $physicalDepthMm, float $heightMm, string $actorId, DateTimeImmutable $now): array
    {
        $this->assertGrid($warehouseNumber, $levels, $slots, $depths);
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->fetchOne('SELECT 1 FROM wms_warehouse_aisle a INNER JOIN wms_warehouse_area ar ON ar.id = a.area_id WHERE a.id = :aisleId AND ar.id = :areaId AND ar.warehouse_id = :warehouseId AND a.tenant_id = :tenantId AND ar.tenant_id = :tenantId', ['aisleId' => $aisleId, 'areaId' => $areaId, 'warehouseId' => $warehouseId, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('Warehouse, area and aisle must form a valid tenant hierarchy.');
        }
        $description = $this->name($description);
        $zoneCode = $this->code($zoneCode, 30);
        $locationType = $this->type($locationType, ['storage', 'receiving', 'shipping', 'quality', 'blocked']);
        $existingCoordinates = [];
        foreach ($this->connection->fetchFirstColumn('SELECT coordinate FROM wms_storage_location WHERE tenant_id = :tenantId AND coordinate IS NOT NULL', ['tenantId' => $tenantId]) as $existingCoordinate) {
            if (is_string($existingCoordinate)) {
                $existingCoordinates[$existingCoordinate] = true;
            }
        }
        $created = 0;
        $skipped = 0;

        $this->connection->transactional(function () use ($tenantId, $warehouseId, $areaId, $aisleId, $warehouseNumber, $levels, $slots, $depths, $description, $zoneCode, $locationType, $widthMm, $physicalDepthMm, $heightMm, $actorId, $now, $existingCoordinates, &$created, &$skipped): void {
            for ($level = 1; $level <= $levels; ++$level) {
                for ($slot = 1; $slot <= $slots; ++$slot) {
                    for ($depth = 1; $depth <= $depths; ++$depth) {
                        $coordinate = $this->coordinate($warehouseNumber, $level, $slot, $depth);
                        if (isset($existingCoordinates[$coordinate])) {
                            ++$skipped;

                            continue;
                        }

                        $this->connection->insert('wms_storage_location', [
                            'id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId,
                            'warehouse_id' => $warehouseId, 'area_id' => $areaId, 'aisle_id' => $aisleId,
                            'code' => $coordinate, 'level_code' => (string) $level,
                            'bin_code' => sprintf('%04d-%04d', $slot, $depth), 'location_type' => $locationType,
                            'capacity_quantity' => 0, 'warehouse_number' => $warehouseNumber,
                            'level_number' => $level, 'slot_number' => $slot, 'depth_number' => $depth,
                            'coordinate' => $coordinate, 'description' => $description, 'zone_code' => $zoneCode,
                            'width_mm' => $this->dimension($widthMm), 'physical_depth_mm' => $this->dimension($physicalDepthMm),
                            'height_mm' => $this->dimension($heightMm), 'putaway_enabled' => 1, 'putaway_priority' => 100,
                            'created_by' => $actorId, 'created_at' => $this->date($now),
                        ]);
                        $existingCoordinates[$coordinate] = true;
                        ++$created;
                    }
                }
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }

    /**
     * @return array{rows: int, locations: int, created: int, skipped: int}
     */
    public function importCsv(string $tenantId, string $warehouseId, string $csv, bool $dryRun, string $actorId, DateTimeImmutable $now): array
    {
        if (!$this->referenceExists('wms_warehouse', $warehouseId, $tenantId)) {
            throw new InventoryReferenceNotFoundException('The warehouse does not exist in the tenant.');
        }
        $this->assertActor($tenantId, $actorId);
        $rows = $this->csvRows($csv);
        $locations = 0;
        foreach ($rows as $row) {
            $warehouseNumber = $this->csvInteger($row, 'Lagernummer');
            $levels = $this->csvInteger($row, 'Fachboden');
            $slots = $this->csvInteger($row, 'Stellplatz');
            $depths = $this->csvInteger($row, 'Tiefe');
            $this->assertGrid($warehouseNumber, $levels, $slots, $depths);
            $this->name($this->csvString($row, 'Bezeichnung'), 100);
            $this->name($this->csvString($row, 'Bezeichnung Lang'), 100);
            $this->code($this->csvString($row, 'Lager-Model'), 20);
            $this->code($this->csvString($row, 'Lager-Typ'), 30);
            $locations += $levels * $slots * $depths;
        }
        if ($dryRun) {
            return ['rows' => count($rows), 'locations' => $locations, 'created' => 0, 'skipped' => 0];
        }

        $created = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $zoneCode = $this->code($this->csvString($row, 'Lager-Typ'), 30);
            $areaId = $this->ensureArea($tenantId, $warehouseId, $zoneCode, $this->csvString($row, 'Bezeichnung Lang'), $actorId, $now);
            $warehouseNumber = $this->csvInteger($row, 'Lagernummer');
            $aisleId = $this->ensureAisle($tenantId, $areaId, (string) $warehouseNumber, $this->csvString($row, 'Bezeichnung'), $this->csvString($row, 'Lager-Model'), $actorId, $now);
            $result = $this->generateGrid($tenantId, $warehouseId, $areaId, $aisleId, $warehouseNumber, $this->csvInteger($row, 'Fachboden'), $this->csvInteger($row, 'Stellplatz'), $this->csvInteger($row, 'Tiefe'), $this->csvString($row, 'Bezeichnung Lang'), $zoneCode, $zoneCode === 'WAZ' ? 'shipping' : 'storage', 0, 0, 0, $actorId, $now);
            $created += $result['created'];
            $skipped += $result['skipped'];
        }

        return ['rows' => count($rows), 'locations' => $locations, 'created' => $created, 'skipped' => $skipped];
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
            default => throw new InvalidArgumentException('The topology resource is not supported.'),
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

        $warehouseNumber = $this->integer($data, 'warehouse_number');
        $levelNumber = $this->integer($data, 'level_number');
        $slotNumber = $this->integer($data, 'slot_number');
        $depthNumber = $this->integer($data, 'depth_number');
        $coordinate = $this->coordinate($warehouseNumber, $levelNumber, $slotNumber, $depthNumber);
        $bin = new StorageBinDefinition($coordinate, (string) $levelNumber, sprintf('%04d-%04d', $slotNumber, $depthNumber), $this->string($data, 'location_type'), 0);

        return ['warehouse_id' => $warehouseId, 'area_id' => $areaId, 'aisle_id' => $aisleId, 'code' => $bin->code(), 'level_code' => $bin->levelCode(), 'bin_code' => $bin->binCode(), 'location_type' => $bin->locationType(), 'capacity_quantity' => 0, 'warehouse_number' => $warehouseNumber, 'level_number' => $levelNumber, 'slot_number' => $slotNumber, 'depth_number' => $depthNumber, 'coordinate' => $coordinate, 'description' => $this->name($this->string($data, 'description')), 'zone_code' => $this->code($this->string($data, 'zone_code'), 30), 'width_mm' => $this->dimension($this->decimal($data, 'width_mm')), 'physical_depth_mm' => $this->dimension($this->decimal($data, 'physical_depth_mm')), 'height_mm' => $this->dimension($this->decimal($data, 'height_mm')), 'putaway_enabled' => $this->boolean($data, 'putaway_enabled') ? 1 : 0, 'putaway_priority' => $this->integer($data, 'putaway_priority')];
    }

    private function table(string $resource): string
    {
        return match ($resource) {
            'site' => 'wms_site', 'warehouse' => 'wms_warehouse', 'area' => 'wms_warehouse_area',
            'aisle' => 'wms_warehouse_aisle', 'bin' => 'wms_storage_location',
            default => throw new InvalidArgumentException('The topology resource is not supported.'),
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
            throw new InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
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
            throw new InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function boolean(array $data, string $field): bool
    {
        return filter_var($data[$field] ?? false, FILTER_VALIDATE_BOOL);
    }

    /** @param array<string, mixed> $data */
    private function decimal(array $data, string $field): float
    {
        $value = $data[$field] ?? null;
        if (!is_int($value) && !is_float($value) && (!is_string($value) || !is_numeric($value))) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be numeric.', $field));
        }

        return (float) $value;
    }

    private function timezone(string $timezone): string
    {
        if (!in_array($timezone, timezone_identifiers_list(), true)) {
            throw new InvalidArgumentException('The site timezone is invalid.');
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
            throw new InvalidArgumentException('The topology code is invalid.');
        }

        return $code;
    }

    private function name(string $name, int $maxLength = 255): string
    {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > $maxLength) {
            throw new InvalidArgumentException('The topology name is invalid.');
        }

        return $name;
    }

    /** @param list<string> $supported */
    private function type(string $type, array $supported): string
    {
        if (!in_array($type, $supported, true)) {
            throw new InvalidArgumentException('The topology type is not supported.');
        }

        return $type;
    }

    private function coordinate(int $warehouseNumber, int $level, int $slot, int $depth): string
    {
        return new StorageLocationCoordinate($warehouseNumber, $level, $slot, $depth)->value();
    }

    private function assertGrid(int $warehouseNumber, int $levels, int $slots, int $depths): void
    {
        if ($warehouseNumber < 1 || $warehouseNumber > 999 || $levels < 1 || $levels > 9999 || $slots < 1 || $slots > 9999 || $depths < 1 || $depths > 9999) {
            throw new InvalidArgumentException('Warehouse number, levels, slots and depths must fit the V2 coordinate format.');
        }

        if ($levels * $slots * $depths > 100000) {
            throw new InvalidArgumentException('A single topology grid must not create more than 100,000 storage locations.');
        }
    }

    private function dimension(?float $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value < 0 || $value > 999999.99) {
            throw new InvalidArgumentException('A storage location dimension is invalid.');
        }

        return number_format($value, 2, '.', '');
    }

    /** @return list<array<string, string>> */
    private function csvRows(string $csv): array
    {
        $stream = fopen('php://temp', 'w+b');
        if ($stream === false) {
            throw new InvalidArgumentException('The CSV input cannot be read.');
        }
        fwrite($stream, preg_replace('/^\xEF\xBB\xBF/', '', $csv) ?? $csv);
        rewind($stream);
        $headers = fgetcsv($stream, separator: ',', escape: '');
        $required = ['Lagernummer', 'Bezeichnung', 'Fachboden', 'Stellplatz', 'Tiefe', 'Lager-Model', 'Lager-Typ', 'Bezeichnung Lang', 'Letzte Änderung'];
        if ($headers !== $required) {
            throw new InvalidArgumentException('The topology CSV headers do not match the expected export format.');
        }
        $headers = $required;

        $rows = [];
        while (($values = fgetcsv($stream, separator: ',', escape: '')) !== false) {
            if ($values === [null] || $values === []) {
                continue;
            }
            if (count($values) !== count($headers)) {
                throw new InvalidArgumentException('A topology CSV row has an invalid number of columns.');
            }
            $combined = array_combine($headers, array_map(static fn (?string $value): string => trim((string) $value), $values));
            if ($combined === false) {
                throw new InvalidArgumentException('A topology CSV row cannot be mapped to its headers.');
            }
            /** @var array<string, string> $row */
            $row = $combined;
            $rows[] = $row;
        }
        fclose($stream);
        if ($rows === []) {
            throw new InvalidArgumentException('The topology CSV does not contain any data rows.');
        }

        return $rows;
    }

    /** @param array<string, string> $row */
    private function csvInteger(array $row, string $field): int
    {
        $value = $this->csvString($row, $field);
        if (!ctype_digit($value)) {
            throw new InvalidArgumentException(sprintf('CSV field "%s" must be a positive integer.', $field));
        }

        return (int) $value;
    }

    /** @param array<string, string> $row */
    private function csvString(array $row, string $field): string
    {
        $value = trim($row[$field] ?? '');
        if ($value === '') {
            throw new InvalidArgumentException(sprintf('CSV field "%s" must not be empty.', $field));
        }

        return $value;
    }

    private function ensureArea(string $tenantId, string $warehouseId, string $code, string $name, string $actorId, DateTimeImmutable $now): string
    {
        $id = $this->connection->fetchOne('SELECT id FROM wms_warehouse_area WHERE tenant_id = :tenantId AND warehouse_id = :warehouseId AND code = :code', ['tenantId' => $tenantId, 'warehouseId' => $warehouseId, 'code' => $code]);
        if (is_string($id)) {
            return $id;
        }

        $id = Uuid::v7()->toRfc4122();
        $this->createArea($id, $tenantId, $warehouseId, $code, $name, $code === 'WAZ' ? 'shipping' : 'storage', $actorId, $now);

        return $id;
    }

    private function ensureAisle(string $tenantId, string $areaId, string $code, string $name, string $storageModel, string $actorId, DateTimeImmutable $now): string
    {
        $id = $this->connection->fetchOne('SELECT id FROM wms_warehouse_aisle WHERE tenant_id = :tenantId AND area_id = :areaId AND code = :code', ['tenantId' => $tenantId, 'areaId' => $areaId, 'code' => $code]);
        if (!is_string($id)) {
            $id = Uuid::v7()->toRfc4122();
            $this->createAisle($id, $tenantId, $areaId, $code, $name, $actorId, $now);
        }
        $this->connection->update('wms_warehouse_aisle', ['storage_model' => $this->code($storageModel, 20)], ['id' => $id, 'tenant_id' => $tenantId]);

        return $id;
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
