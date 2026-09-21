<?php

declare(strict_types=1);

namespace WebWMS\Fulfillment\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Application\TransferStockCommand;
use WebWMS\Inventory\Application\TransferStockHandler;

final readonly class InternalTransportService
{
    public function __construct(private Connection $connection, private TransferStockHandler $transferStock)
    {
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function workspace(string $tenantId): array
    {
        return [
            'orders' => $this->connection->fetchAllAssociative(
                'SELECT o.*, s.code source_code, t.code target_code, p.sku, f.code forklift_code FROM wms_transport_order o '
                . 'INNER JOIN wms_storage_location s ON s.id = o.source_location_id INNER JOIN wms_storage_location t ON t.id = o.target_location_id '
                . 'LEFT JOIN wms_product_reference p ON p.id = o.product_id LEFT JOIN wms_forklift f ON f.id = o.forklift_id '
                . 'WHERE o.tenant_id = :tenantId ORDER BY FIELD(o.status, \'started\', \'assigned\', \'open\', \'completed\'), o.priority DESC, o.due_at, o.created_at',
                ['tenantId' => $tenantId],
            ),
            'forklifts' => $this->all('wms_forklift', $tenantId, 'code'),
            'rules' => $this->all('wms_transport_rule', $tenantId, 'priority DESC, code'),
            'stations' => $this->connection->fetchAllAssociative('SELECT s.*, l.code location_code, w.code warehouse_code FROM wms_process_station s INNER JOIN wms_storage_location l ON l.id = s.location_id INNER JOIN wms_warehouse w ON w.id = s.warehouse_id WHERE s.tenant_id = :tenantId ORDER BY s.sequence_number, s.code', ['tenantId' => $tenantId]),
            'milkRuns' => $this->connection->fetchAllAssociative('SELECT r.*, w.code warehouse_code, COUNT(s.id) stop_count FROM wms_milk_run r INNER JOIN wms_warehouse w ON w.id = r.warehouse_id LEFT JOIN wms_milk_run_stop s ON s.milk_run_id = r.id WHERE r.tenant_id = :tenantId GROUP BY r.id, w.code ORDER BY r.code', ['tenantId' => $tenantId]),
            'locations' => $this->connection->fetchAllAssociative('SELECT l.id, l.code, l.warehouse_id, w.code warehouse_code FROM wms_storage_location l INNER JOIN wms_warehouse w ON w.id = l.warehouse_id WHERE l.tenant_id = :tenantId ORDER BY w.code, l.code', ['tenantId' => $tenantId]),
            'warehouses' => $this->connection->fetchAllAssociative('SELECT id, code, name FROM wms_warehouse WHERE tenant_id = :tenantId ORDER BY code', ['tenantId' => $tenantId]),
            'products' => $this->connection->fetchAllAssociative('SELECT id, sku, name FROM wms_product_reference WHERE tenant_id = :tenantId ORDER BY sku LIMIT 500', ['tenantId' => $tenantId]),
            'replenishments' => $this->connection->fetchAllAssociative('SELECT o.id, p.code policy_code, pr.sku, s.code source_code, t.code target_code, o.quantity, o.status, o.created_at FROM wms_replenishment_order o INNER JOIN wms_replenishment_policy p ON p.id = o.policy_id INNER JOIN wms_product_reference pr ON pr.id = o.product_id INNER JOIN wms_storage_location s ON s.id = o.source_location_id INNER JOIN wms_storage_location t ON t.id = o.target_location_id WHERE o.tenant_id = :tenantId ORDER BY o.created_at DESC LIMIT 100', ['tenantId' => $tenantId]),
            'replenishmentPolicies' => $this->connection->fetchAllAssociative('SELECT p.*, pr.sku, l.code target_location_code, w.code warehouse_code FROM wms_replenishment_policy p INNER JOIN wms_product_reference pr ON pr.id = p.product_id INNER JOIN wms_storage_location l ON l.id = p.target_location_id INNER JOIN wms_warehouse w ON w.id = p.warehouse_id WHERE p.tenant_id = :tenantId ORDER BY p.priority DESC, p.code', ['tenantId' => $tenantId]),
        ];
    }

    /** @param array<string, mixed> $data */
    public function createResource(string $tenantId, string $actorId, string $resource, array $data, DateTimeImmutable $now): string
    {
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $resource, $data, $now, $id): void {
            if ($resource === 'forklift') {
                $warehouseId = $this->tenantReference($connection, 'wms_warehouse', $tenantId, $data, 'warehouse_id');
                $connection->insert('wms_forklift', ['id' => $id, 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId, 'code' => $this->code($data, 'code'), 'name' => $this->text($data, 'name', 100), 'resource_type' => $this->choice($data, 'resource_type', ['forklift', 'tugger_train', 'agv']), 'status' => 'available', 'assigned_user_id' => null, 'last_location_id' => null, 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            } elseif ($resource === 'rule') {
                $connection->insert('wms_transport_rule', ['id' => $id, 'tenant_id' => $tenantId, 'code' => $this->code($data, 'code'), 'name' => $this->text($data, 'name', 100), 'trigger_type' => $this->choice($data, 'trigger_type', ['manual', 'replenishment', 'prepositioning', 'material_flow']), 'source_prefix' => $this->text($data, 'source_prefix', 50), 'target_prefix' => $this->text($data, 'target_prefix', 50), 'transport_type' => $this->choice($data, 'transport_type', self::transportTypes()), 'resource_type' => $this->choice($data, 'resource_type', ['forklift', 'tugger_train', 'agv']), 'priority' => $this->positiveInt($data, 'priority', 999), 'enabled' => 1, 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            } elseif ($resource === 'station') {
                $warehouseId = $this->tenantReference($connection, 'wms_warehouse', $tenantId, $data, 'warehouse_id');
                $locationId = $this->tenantReference($connection, 'wms_storage_location', $tenantId, $data, 'location_id');
                if ($connection->fetchOne('SELECT 1 FROM wms_storage_location WHERE id = :locationId AND warehouse_id = :warehouseId', ['locationId' => $locationId, 'warehouseId' => $warehouseId]) === false) {
                    throw new \InvalidArgumentException('Station und Lagerplatz gehören nicht zum selben Lager.');
                }
                $connection->insert('wms_process_station', ['id' => $id, 'tenant_id' => $tenantId, 'warehouse_id' => $warehouseId, 'location_id' => $locationId, 'code' => $this->code($data, 'code'), 'name' => $this->text($data, 'name', 100), 'station_type' => $this->choice($data, 'station_type', ['storage', 'picking', 'consolidation', 'packing', 'shipping', 'buffer']), 'sequence_number' => $this->positiveInt($data, 'sequence_number', 9999), 'active' => 1, 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            } elseif ($resource === 'milk_run') {
                $warehouseId = $this->tenantReference($connection, 'wms_warehouse', $tenantId, $data, 'warehouse_id');
                $schedule = $this->choice($data, 'schedule_type', ['fixed', 'dynamic']);
                $connection->insert('wms_milk_run', ['id' => $id, 'tenant_id' => $tenantId, 'code' => $this->code($data, 'code'), 'name' => $this->text($data, 'name', 100), 'warehouse_id' => $warehouseId, 'schedule_type' => $schedule, 'interval_minutes' => $schedule === 'fixed' ? $this->positiveInt($data, 'interval_minutes', 1440) : null, 'next_departure_at' => null, 'status' => 'active', 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            } else {
                throw new \InvalidArgumentException('Die Transportressource ist unbekannt.');
            }
            $this->audit($connection, $tenantId, $actorId, $resource, $id, 'created', [], $now);
        });

        return $id;
    }

    /** @param array<string, mixed> $data */
    public function createOrder(string $tenantId, string $actorId, array $data, DateTimeImmutable $now): string
    {
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $data, $now, $id): void {
            $source = $this->tenantReference($connection, 'wms_storage_location', $tenantId, $data, 'source_location_id');
            $target = $this->tenantReference($connection, 'wms_storage_location', $tenantId, $data, 'target_location_id');
            if ($source === $target) {
                throw new \InvalidArgumentException('Quell- und Zielplatz müssen verschieden sein.');
            }
            $productId = $this->optionalReference($connection, 'wms_product_reference', $tenantId, $data, 'product_id');
            $quantity = $productId === null ? null : $this->positiveInt($data, 'quantity', PHP_INT_MAX);
            $connection->insert('wms_transport_order', ['id' => $id, 'tenant_id' => $tenantId, 'code' => $this->code($data, 'code', 50), 'transport_type' => $this->choice($data, 'transport_type', self::transportTypes()), 'trigger_reference' => $this->nullable($data, 'trigger_reference', 100), 'product_id' => $productId, 'source_location_id' => $source, 'target_location_id' => $target, 'quantity' => $quantity, 'stock_status' => $productId === null ? null : $this->nullable($data, 'stock_status', 30) ?? 'available', 'batch_number' => $this->nullable($data, 'batch_number', 100), 'serial_number' => $this->nullable($data, 'serial_number', 100), 'expires_at' => $this->nullable($data, 'expires_at', 10), 'forklift_id' => null, 'priority' => $this->positiveInt($data, 'priority', 999), 'due_at' => $this->nullable($data, 'due_at', 30), 'status' => 'open', 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            $this->audit($connection, $tenantId, $actorId, 'transport_order', $id, 'created', ['transport_type' => $data['transport_type'] ?? null], $now);
        });

        return $id;
    }

    /** @param array<string, mixed> $data */
    public function createOrderFromRule(string $tenantId, string $actorId, string $triggerType, array $data, DateTimeImmutable $now): string
    {
        $source = $this->text($data, 'source_location_id', 36);
        $target = $this->text($data, 'target_location_id', 36);
        $rule = $this->connection->fetchAssociative(
            'SELECT r.* FROM wms_transport_rule r INNER JOIN wms_storage_location s ON s.tenant_id = r.tenant_id AND s.id = :sourceId INNER JOIN wms_storage_location t ON t.tenant_id = r.tenant_id AND t.id = :targetId WHERE r.tenant_id = :tenantId AND r.trigger_type = :triggerType AND r.enabled = 1 AND s.code LIKE CONCAT(r.source_prefix, \'%\') AND t.code LIKE CONCAT(r.target_prefix, \'%\') ORDER BY r.priority DESC, r.code LIMIT 1',
            ['sourceId' => $source, 'targetId' => $target, 'tenantId' => $tenantId, 'triggerType' => $triggerType],
        );
        if ($rule === false) {
            throw new \DomainException('Für den Bedarf wurde keine aktive Transportregel gefunden.');
        }
        $data['transport_type'] = $rule['transport_type'];
        $data['priority'] = $rule['priority'];

        return $this->createOrder($tenantId, $actorId, $data, $now);
    }

    public function addMilkRunStop(string $tenantId, string $actorId, string $milkRunId, string $stationId, int $sequence, int $dwellMinutes, DateTimeImmutable $now): string
    {
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $milkRunId, $stationId, $sequence, $dwellMinutes, $now, $id): void {
            if ($connection->fetchOne('SELECT 1 FROM wms_milk_run r INNER JOIN wms_process_station s ON s.tenant_id = r.tenant_id AND s.warehouse_id = r.warehouse_id WHERE r.id = :runId AND s.id = :stationId AND r.tenant_id = :tenantId', ['runId' => $milkRunId, 'stationId' => $stationId, 'tenantId' => $tenantId]) === false) {
                throw new \InvalidArgumentException('Routenzug und Station müssen demselben Mandanten und Lager angehören.');
            }
            $connection->insert('wms_milk_run_stop', ['id' => $id, 'milk_run_id' => $milkRunId, 'station_id' => $stationId, 'sequence_number' => max(1, $sequence), 'dwell_minutes' => max(0, min(120, $dwellMinutes))]);
            $this->audit($connection, $tenantId, $actorId, 'milk_run', $milkRunId, 'stop_added', ['station_id' => $stationId, 'sequence' => $sequence], $now);
        });

        return $id;
    }

    /** @return list<string> */
    public function dispatchMilkRun(string $tenantId, string $actorId, string $milkRunId, DateTimeImmutable $now): array
    {
        $stops = $this->connection->fetchAllAssociative('SELECT s.sequence_number, p.location_id FROM wms_milk_run_stop s INNER JOIN wms_milk_run r ON r.id = s.milk_run_id INNER JOIN wms_process_station p ON p.id = s.station_id WHERE r.id = :runId AND r.tenant_id = :tenantId AND r.status = \'active\' ORDER BY s.sequence_number', ['runId' => $milkRunId, 'tenantId' => $tenantId]);
        if (count($stops) < 2) {
            throw new \DomainException('Eine Routenzugtour benötigt mindestens zwei Stationen.');
        }
        $ids = [];
        for ($index = 1, $count = count($stops); $index < $count; ++$index) {
            $ids[] = $this->createOrder($tenantId, $actorId, ['code' => sprintf('mr-%s-%02d-%s', substr($milkRunId, 0, 8), $index, $now->format('His')), 'transport_type' => 'milk_run', 'trigger_reference' => $milkRunId, 'source_location_id' => $stops[$index - 1]['location_id'], 'target_location_id' => $stops[$index]['location_id'], 'priority' => 50], $now);
        }

        return $ids;
    }

    public function assign(string $tenantId, string $actorId, string $orderId, string $forkliftId, DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $orderId, $forkliftId, $now): void {
            if ($connection->fetchOne("SELECT 1 FROM wms_forklift WHERE id = :id AND tenant_id = :tenantId AND status = 'available' FOR UPDATE", ['id' => $forkliftId, 'tenantId' => $tenantId]) === false) {
                throw new \DomainException('Die Ressource ist nicht verfügbar.');
            }
            $updated = $connection->executeStatement("UPDATE wms_transport_order SET forklift_id = :forkliftId, status = 'assigned', assigned_by = :actorId, assigned_at = :now WHERE id = :id AND tenant_id = :tenantId AND status = 'open'", ['forkliftId' => $forkliftId, 'actorId' => $actorId, 'now' => $this->date($now), 'id' => $orderId, 'tenantId' => $tenantId]);
            if ($updated !== 1) {
                throw new \DomainException('Nur offene Fahrbefehle können zugewiesen werden.');
            }
            $connection->update('wms_forklift', ['status' => 'busy', 'changed_at' => $this->date($now)], ['id' => $forkliftId, 'tenant_id' => $tenantId]);
            $this->audit($connection, $tenantId, $actorId, 'transport_order', $orderId, 'assigned', ['forklift_id' => $forkliftId], $now);
        });
    }

    public function start(string $tenantId, string $actorId, string $orderId, DateTimeImmutable $now): void
    {
        $updated = $this->connection->executeStatement("UPDATE wms_transport_order SET status = 'started', started_by = :actorId, started_at = :now WHERE id = :id AND tenant_id = :tenantId AND status = 'assigned'", ['actorId' => $actorId, 'now' => $this->date($now), 'id' => $orderId, 'tenantId' => $tenantId]);
        if ($updated !== 1) {
            throw new \DomainException('Nur zugewiesene Fahrbefehle können gestartet werden.');
        }
    }

    public function complete(string $tenantId, string $actorId, string $orderId, DateTimeImmutable $now): void
    {
        $order = $this->connection->transactional(function (Connection $connection) use ($tenantId, $orderId): array {
            $row = $connection->fetchAssociative("SELECT * FROM wms_transport_order WHERE id = :id AND tenant_id = :tenantId AND status = 'started' FOR UPDATE", ['id' => $orderId, 'tenantId' => $tenantId]);
            if ($row === false) {
                throw new \DomainException('Nur gestartete Fahrbefehle können quittiert werden.');
            }
            $connection->update('wms_transport_order', ['status' => 'executing'], ['id' => $orderId, 'tenant_id' => $tenantId]);

            return $row;
        });
        $transferId = null;
        try {
            if (is_string($order['product_id']) && is_int($quantity = filter_var($order['quantity'], FILTER_VALIDATE_INT))) {
                $transferId = Uuid::v7()->toRfc4122();
                ($this->transferStock)(new TransferStockCommand($transferId, Uuid::v7()->toRfc4122(), Uuid::v7()->toRfc4122(), $tenantId, $order['product_id'], (string) $order['source_location_id'], (string) $order['target_location_id'], $quantity, 'Transportauftrag ' . $order['code'], $actorId, $now, (string) ($order['stock_status'] ?? 'available'), (string) ($order['stock_status'] ?? 'available'), is_string($order['batch_number']) ? $order['batch_number'] : null, is_string($order['serial_number']) ? $order['serial_number'] : null, is_string($order['expires_at']) ? new DateTimeImmutable($order['expires_at']) : null));
            }
        } catch (\Throwable $exception) {
            $this->connection->update('wms_transport_order', ['status' => 'started'], ['id' => $orderId, 'tenant_id' => $tenantId, 'status' => 'executing']);
            throw $exception;
        }
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $orderId, $order, $transferId, $now): void {
            $connection->update('wms_transport_order', ['status' => 'completed', 'completed_by' => $actorId, 'completed_at' => $this->date($now), 'transfer_id' => $transferId], ['id' => $orderId, 'tenant_id' => $tenantId, 'status' => 'executing']);
            if (is_string($order['forklift_id'])) {
                $connection->update('wms_forklift', ['status' => 'available', 'last_location_id' => $order['target_location_id'], 'changed_at' => $this->date($now)], ['id' => $order['forklift_id'], 'tenant_id' => $tenantId]);
            }
            $this->audit($connection, $tenantId, $actorId, 'transport_order', $orderId, 'completed', ['transfer_id' => $transferId], $now);
        });
    }

    /** @return list<string> */
    private static function transportTypes(): array
    {
        return ['relocation', 'replenishment', 'prepositioning', 'material_flow', 'milk_run'];
    }

    /** @return list<array<string, mixed>> */
    private function all(string $table, string $tenantId, string $order): array
    {
        return $this->connection->fetchAllAssociative(sprintf('SELECT * FROM %s WHERE tenant_id = :tenantId ORDER BY %s', $table, $order), ['tenantId' => $tenantId]);
    }

    /** @param array<string, mixed> $data */
    private function tenantReference(Connection $connection, string $table, string $tenantId, array $data, string $field): string
    {
        $id = $this->text($data, $field, 36);
        if ($connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id AND tenant_id = :tenantId', $table), ['id' => $id, 'tenantId' => $tenantId]) === false) {
            throw new \InvalidArgumentException(sprintf('Die Referenz "%s" gehört nicht zum Mandanten.', $field));
        }

        return $id;
    }

    /** @param array<string, mixed> $data */
    private function optionalReference(Connection $connection, string $table, string $tenantId, array $data, string $field): ?string
    {
        $value = $this->nullable($data, $field, 36);

        return $value === null ? null : $this->tenantReference($connection, $table, $tenantId, [$field => $value], $field);
    }

    /** @param array<string, mixed> $data */
    private function code(array $data, string $field, int $maximum = 30): string
    {
        $value = strtolower($this->text($data, $field, $maximum));
        if (preg_match('/^[a-z0-9][a-z0-9._-]*$/', $value) !== 1) {
            throw new \InvalidArgumentException('Der Code ist ungültig.');
        }

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function text(array $data, string $field, int $maximum): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || trim($value) === '' || mb_strlen(trim($value)) > $maximum) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich oder zu lang.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $data */
    private function nullable(array $data, string $field, int $maximum): ?string
    {
        $value = $data[$field] ?? null;
        if ($value === null || $value === '') {
            return null;
        }

        return $this->text($data, $field, $maximum);
    }

    /** @param array<string, mixed> $data @param list<string> $choices */
    private function choice(array $data, string $field, array $choices): string
    {
        $value = $this->text($data, $field, 50);
        if (!in_array($value, $choices, true)) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält einen ungültigen Wert.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function positiveInt(array $data, string $field, int $maximum): int
    {
        $value = filter_var($data[$field] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($value) || $value < 1 || $value > $maximum) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" muss eine positive Ganzzahl sein.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function audit(Connection $connection, string $tenantId, string $actorId, string $type, string $id, string $event, array $payload, DateTimeImmutable $now): void
    {
        $connection->insert('wms_fulfillment_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => $type, 'aggregate_id' => $id, 'event_type' => $event, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $this->date($now)]);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
