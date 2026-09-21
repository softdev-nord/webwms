<?php

declare(strict_types=1);

namespace WebWMS\Fulfillment\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class AdvancedPickingService
{
    public function __construct(private Connection $connection)
    {
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function workspace(string $tenantId): array
    {
        return [
            'waves' => $this->connection->fetchAllAssociative(
                'SELECT w.*, COUNT(wl.pick_list_id) pick_list_count, COUNT(DISTINCT l.outbound_order_id) order_count, '
                . "COALESCE(SUM(CASE WHEN l.status = 'completed' THEN 1 ELSE 0 END), 0) completed_pick_lists, "
                . "COALESCE(SUM(CASE WHEN wl.consolidation_status = 'completed' THEN 1 ELSE 0 END), 0) consolidated_count "
                . 'FROM wms_pick_wave w LEFT JOIN wms_pick_wave_list wl ON wl.wave_id = w.id LEFT JOIN wms_pick_list l ON l.id = wl.pick_list_id '
                . 'WHERE w.tenant_id = :tenantId GROUP BY w.id ORDER BY w.priority DESC, w.planned_start_at, w.created_at DESC',
                ['tenantId' => $tenantId],
            ),
            'availablePickLists' => $this->connection->fetchAllAssociative(
                'SELECT l.id, l.code, l.status, o.order_number FROM wms_pick_list l INNER JOIN wms_outbound_order o ON o.id = l.outbound_order_id '
                . 'WHERE l.tenant_id = :tenantId AND l.status IN (:statuses) AND NOT EXISTS (SELECT 1 FROM wms_pick_wave_list wl WHERE wl.pick_list_id = l.id) ORDER BY l.created_at',
                ['tenantId' => $tenantId, 'statuses' => ['open', 'assigned']],
                ['statuses' => \Doctrine\DBAL\ArrayParameterType::STRING],
            ),
            'controlTower' => $this->connection->fetchAllAssociative(
                'SELECT l.id, l.code, l.status, l.assigned_to, u.display_name assigned_to_name, o.order_number, '
                . 'w.id wave_id, w.code wave_code, w.strategy wave_strategy, w.status wave_status, wl.container_code, wl.consolidation_status, COUNT(t.id) task_count, '
                . "COALESCE(SUM(CASE WHEN t.status = 'open' THEN 1 ELSE 0 END), 0) open_task_count, "
                . "COALESCE(SUM(CASE WHEN t.status = 'shortage' THEN 1 ELSE 0 END), 0) shortage_count "
                . 'FROM wms_pick_list l INNER JOIN wms_outbound_order o ON o.id = l.outbound_order_id '
                . 'LEFT JOIN wms_user_account u ON u.id = l.assigned_to LEFT JOIN wms_pick_task t ON t.pick_list_id = l.id '
                . 'LEFT JOIN wms_pick_wave_list wl ON wl.pick_list_id = l.id LEFT JOIN wms_pick_wave w ON w.id = wl.wave_id '
                . 'WHERE l.tenant_id = :tenantId GROUP BY l.id, l.code, l.status, l.assigned_to, u.display_name, o.order_number, w.id, w.code, w.strategy, w.status, wl.container_code, wl.consolidation_status '
                . 'ORDER BY shortage_count DESC, open_task_count DESC, l.updated_at',
                ['tenantId' => $tenantId],
            ),
        ];
    }

    /** @param list<string> $pickListIds */
    public function createWave(string $tenantId, string $actorId, string $code, string $name, string $strategy, string $selectionType, ?string $selectionValue, int $priority, ?DateTimeImmutable $plannedStart, array $pickListIds, DateTimeImmutable $now): string
    {
        $pickListIds = array_values(array_unique($pickListIds));
        if ($pickListIds === []) {
            throw new \InvalidArgumentException('Eine Pickwelle benötigt mindestens eine Pickliste.');
        }
        $strategy = $this->choice($strategy, ['single_order', 'multi_order', 'two_stage']);
        $selectionType = $this->choice($selectionType, ['manual', 'time', 'tour', 'carrier', 'priority']);
        if ($strategy === 'single_order' && count($pickListIds) !== 1) {
            throw new \InvalidArgumentException('Single-Order-Wellen enthalten genau eine Pickliste.');
        }
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $code, $name, $strategy, $selectionType, $selectionValue, $priority, $plannedStart, $pickListIds, $now, $id): void {
            $eligibleIds = $connection->fetchFirstColumn(
                'SELECT l.id FROM wms_pick_list l WHERE l.tenant_id = :tenantId AND l.id IN (:ids) AND l.status IN (:statuses) AND NOT EXISTS (SELECT 1 FROM wms_pick_wave_list wl WHERE wl.pick_list_id = l.id) FOR UPDATE',
                ['tenantId' => $tenantId, 'ids' => $pickListIds, 'statuses' => ['open', 'assigned']],
                ['ids' => \Doctrine\DBAL\ArrayParameterType::STRING, 'statuses' => \Doctrine\DBAL\ArrayParameterType::STRING],
            );
            if (count($eligibleIds) !== count($pickListIds)) {
                throw new \DomainException('Alle Picklisten müssen offen, mandantenzugehörig und noch ungebündelt sein.');
            }
            $connection->insert('wms_pick_wave', ['id' => $id, 'tenant_id' => $tenantId, 'code' => $this->code($code), 'name' => $this->required($name, 120), 'strategy' => $strategy, 'selection_type' => $selectionType, 'selection_value' => $this->nullable($selectionValue, 100), 'priority' => max(1, min(999, $priority)), 'planned_start_at' => $plannedStart?->format('Y-m-d H:i:s.u'), 'status' => 'planned', 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            foreach ($pickListIds as $index => $pickListId) {
                $connection->insert('wms_pick_wave_list', ['wave_id' => $id, 'pick_list_id' => $pickListId, 'container_code' => sprintf('%s-%02d', strtoupper($this->code($code)), $index + 1), 'consolidation_status' => $strategy === 'two_stage' ? 'pending' : 'not_required']);
            }
            $this->audit($connection, $tenantId, $actorId, 'pick_wave', $id, 'created', ['pick_list_ids' => $pickListIds, 'strategy' => $strategy], $now);
        });

        return $id;
    }

    public function releaseWave(string $tenantId, string $actorId, string $waveId, DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $waveId, $now): void {
            $updated = $connection->executeStatement("UPDATE wms_pick_wave SET status = 'released', released_by = :actorId, released_at = :now WHERE id = :id AND tenant_id = :tenantId AND status = 'planned'", ['actorId' => $actorId, 'now' => $this->date($now), 'id' => $waveId, 'tenantId' => $tenantId]);
            if ($updated !== 1) {
                throw new \DomainException('Nur geplante Pickwellen können freigegeben werden.');
            }
            $this->audit($connection, $tenantId, $actorId, 'pick_wave', $waveId, 'released', [], $now);
        });
    }

    public function consolidate(string $tenantId, string $actorId, string $waveId, string $pickListId, DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $waveId, $pickListId, $now): void {
            $updated = $connection->executeStatement(
                "UPDATE wms_pick_wave_list wl INNER JOIN wms_pick_wave w ON w.id = wl.wave_id INNER JOIN wms_pick_list l ON l.id = wl.pick_list_id SET wl.consolidation_status = 'completed', wl.consolidated_by = :actorId, wl.consolidated_at = :now WHERE wl.wave_id = :waveId AND wl.pick_list_id = :pickListId AND w.tenant_id = :tenantId AND w.strategy = 'two_stage' AND w.status = 'released' AND l.status = 'completed' AND wl.consolidation_status = 'pending'",
                ['actorId' => $actorId, 'now' => $this->date($now), 'waveId' => $waveId, 'pickListId' => $pickListId, 'tenantId' => $tenantId],
            );
            if ($updated !== 1) {
                throw new \DomainException('Nur vollständig gepickte Positionen einer freigegebenen zweistufigen Welle können konsolidiert werden.');
            }
            $remaining = (int) $connection->fetchOne("SELECT COUNT(*) FROM wms_pick_wave_list WHERE wave_id = :waveId AND consolidation_status = 'pending'", ['waveId' => $waveId]);
            if ($remaining === 0) {
                $connection->update('wms_pick_wave', ['status' => 'completed', 'completed_at' => $this->date($now)], ['id' => $waveId, 'tenant_id' => $tenantId]);
            }
            $this->audit($connection, $tenantId, $actorId, 'pick_wave', $waveId, 'pick_list_consolidated', ['pick_list_id' => $pickListId], $now);
        });
    }

    public function optimizeRoute(string $tenantId, string $actorId, string $pickListId, DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $pickListId, $now): void {
            $tasks = $connection->fetchAllAssociative(
                "SELECT t.id FROM wms_pick_task t INNER JOIN wms_pick_list pl ON pl.id = t.pick_list_id INNER JOIN wms_stock_allocation a ON a.id = t.allocation_id INNER JOIN wms_storage_location l ON l.id = a.location_id LEFT JOIN wms_warehouse_area wa ON wa.id = l.area_id LEFT JOIN wms_warehouse_aisle ai ON ai.id = l.aisle_id WHERE pl.id = :pickListId AND pl.tenant_id = :tenantId AND pl.status IN ('open', 'assigned') ORDER BY wa.code, ai.code, l.level_code, l.bin_code, l.code FOR UPDATE",
                ['pickListId' => $pickListId, 'tenantId' => $tenantId],
            );
            if ($tasks === []) {
                throw new \DomainException('Die Pickliste kann nicht optimiert werden.');
            }
            foreach ($tasks as $index => $task) {
                $connection->update('wms_pick_task', ['sequence_number' => -($index + 1)], ['id' => $task['id']]);
            }
            foreach ($tasks as $index => $task) {
                $connection->update('wms_pick_task', ['sequence_number' => $index + 1], ['id' => $task['id']]);
            }
            $this->audit($connection, $tenantId, $actorId, 'pick_list', $pickListId, 'route_optimized', ['task_count' => count($tasks)], $now);
        });
    }

    /** @return array{valid: bool, message: string} */
    public function validateScan(string $tenantId, string $actorId, string $taskId, string $location, string $product, ?string $batch, ?string $serial, int $quantity, DateTimeImmutable $now): array
    {
        return $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $taskId, $location, $product, $batch, $serial, $quantity, $now): array {
            $task = $connection->fetchAssociative(
                "SELECT t.id, a.quantity, a.batch_number, a.serial_number, p.sku, l.code location_code FROM wms_pick_task t INNER JOIN wms_pick_list pl ON pl.id = t.pick_list_id INNER JOIN wms_stock_allocation a ON a.id = t.allocation_id INNER JOIN wms_product_reference p ON p.id = a.product_id INNER JOIN wms_storage_location l ON l.id = a.location_id WHERE t.id = :taskId AND pl.tenant_id = :tenantId AND t.status = 'open' FOR UPDATE",
                ['taskId' => $taskId, 'tenantId' => $tenantId],
            );
            if ($task === false) {
                throw new \DomainException('Die offene Pickposition wurde nicht gefunden.');
            }
            $valid = hash_equals((string) $task['location_code'], trim($location))
                && hash_equals((string) $task['sku'], trim($product))
                && ($task['batch_number'] === null || hash_equals((string) $task['batch_number'], trim((string) $batch)))
                && ($task['serial_number'] === null || hash_equals((string) $task['serial_number'], trim((string) $serial)))
                && (int) $task['quantity'] === $quantity;
            $message = $valid ? 'Scan erfolgreich geprüft.' : 'Scan stimmt nicht mit Lagerplatz, Artikel, Charge, Seriennummer oder Menge überein.';
            $connection->insert('wms_pick_scan_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'pick_task_id' => $taskId, 'location_scan' => trim($location), 'product_scan' => trim($product), 'batch_scan' => $this->nullable($batch, 100), 'serial_scan' => $this->nullable($serial, 100), 'quantity' => $quantity, 'result' => $valid ? 'accepted' : 'rejected', 'message' => $message, 'scanned_by' => $actorId, 'scanned_at' => $this->date($now)]);

            return ['valid' => $valid, 'message' => $message];
        });
    }

    private function choice(string $value, array $choices): string
    {
        if (!in_array($value, $choices, true)) {
            throw new \InvalidArgumentException('Der ausgewählte Wert ist ungültig.');
        }

        return $value;
    }

    private function code(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '' || strlen($value) > 50 || preg_match('/^[a-z0-9][a-z0-9._-]*$/', $value) !== 1) {
            throw new \InvalidArgumentException('Der Code ist ungültig.');
        }

        return $value;
    }

    private function required(string $value, int $maximum): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > $maximum) {
            throw new \InvalidArgumentException('Ein Pflichtfeld ist leer oder zu lang.');
        }

        return $value;
    }

    private function nullable(?string $value, int $maximum): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $this->required($value, $maximum);
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
