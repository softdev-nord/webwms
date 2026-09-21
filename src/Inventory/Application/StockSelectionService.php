<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Domain\AutomaticAllocationResult;
use WebWMS\Inventory\Domain\InsufficientAvailableStockException;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\StockSelectionRuleDefinition;
use WebWMS\Inventory\Domain\StockSelectionStrategy;

final readonly class StockSelectionService
{
    public function __construct(
        private Connection $connection,
        private AllocateStockHandler $allocateStock,
    ) {
    }

    public function createRule(
        string $id,
        string $tenantId,
        string $code,
        string $name,
        string $strategy,
        int $priority,
        bool $enabled,
        ?string $warehouseId,
        ?string $productId,
        string $actorId,
        DateTimeImmutable $now,
    ): void {
        $definition = new StockSelectionRuleDefinition($code, $name, StockSelectionStrategy::fromInput($strategy), $priority, $enabled);
        $this->assertTenantReference('wms_user_account', $actorId, $tenantId);
        if ($warehouseId !== null) {
            $this->assertTenantReference('wms_warehouse', $warehouseId, $tenantId);
        }
        if ($productId !== null) {
            $this->assertTenantReference('wms_product_reference', $productId, $tenantId);
        }

        $this->connection->insert('wms_stock_selection_rule', [
            'id' => $id,
            'tenant_id' => $tenantId,
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'code' => $definition->code,
            'name' => $definition->name,
            'strategy' => $definition->strategy->value,
            'priority' => $definition->priority,
            'enabled' => $definition->enabled ? 1 : 0,
            'created_by' => $actorId,
            'created_at' => $now->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function allocate(
        string $tenantId,
        string $reservationId,
        string $ruleId,
        string $actorId,
        DateTimeImmutable $now,
    ): AutomaticAllocationResult {
        return $this->connection->transactional(function () use ($tenantId, $reservationId, $ruleId, $actorId, $now): AutomaticAllocationResult {
            $rule = $this->connection->fetchAssociative(
                'SELECT strategy, warehouse_id, product_id FROM wms_stock_selection_rule '
                . 'WHERE id = :ruleId AND tenant_id = :tenantId AND enabled = 1 FOR UPDATE',
                ['ruleId' => $ruleId, 'tenantId' => $tenantId],
            );
            $reservation = $this->connection->fetchAssociative(
                'SELECT product_id, requested_quantity, allocated_quantity FROM wms_stock_reservation '
                . "WHERE id = :reservationId AND tenant_id = :tenantId AND status IN ('open', 'partially_allocated') FOR UPDATE",
                ['reservationId' => $reservationId, 'tenantId' => $tenantId],
            );
            if ($rule === false || $reservation === false) {
                throw new InventoryReferenceNotFoundException('An enabled stock selection rule and open reservation must exist in the tenant.');
            }

            $productId = $this->string($reservation, 'product_id');
            $ruleProductId = $this->nullableString($rule, 'product_id');
            if ($ruleProductId !== null && $ruleProductId !== $productId) {
                throw new InventoryReferenceNotFoundException('The stock selection rule does not apply to the reserved product.');
            }

            $strategy = StockSelectionStrategy::fromInput($this->string($rule, 'strategy'));
            $requested = $this->integer($reservation, 'requested_quantity') - $this->integer($reservation, 'allocated_quantity');
            $candidates = $this->candidates($tenantId, $productId, $this->nullableString($rule, 'warehouse_id'), $strategy, $now);
            $remaining = $requested;
            foreach ($candidates as $candidate) {
                if ($remaining === 0) {
                    break;
                }
                $available = $this->integer($candidate, 'available_quantity');
                $quantity = min($remaining, $available);
                ($this->allocateStock)(new AllocateStockCommand(
                    Uuid::v7()->toRfc4122(),
                    $reservationId,
                    $tenantId,
                    $productId,
                    $this->string($candidate, 'location_id'),
                    $quantity,
                    $actorId,
                    $now,
                    $this->string($candidate, 'stock_status'),
                    $this->nullableString($candidate, 'batch_number'),
                    $this->nullableString($candidate, 'serial_number'),
                    ($expiresAt = $this->nullableString($candidate, 'expires_at')) === null ? null : new DateTimeImmutable($expiresAt),
                ));
                $remaining -= $quantity;
            }
            if ($remaining > 0) {
                throw new InsufficientAvailableStockException('The selected strategy cannot fully cover the remaining reservation quantity.');
            }

            $allocated = $requested - $remaining;
            $this->connection->insert('wms_stock_selection_event', [
                'id' => Uuid::v7()->toRfc4122(),
                'tenant_id' => $tenantId,
                'rule_id' => $ruleId,
                'reservation_id' => $reservationId,
                'product_id' => $productId,
                'requested_quantity' => $requested,
                'allocated_quantity' => $allocated,
                'candidate_count' => count($candidates),
                'performed_by' => $actorId,
                'occurred_at' => $now->format('Y-m-d H:i:s.u'),
            ]);

            return new AutomaticAllocationResult($strategy->value, $requested, $allocated, $remaining, count($candidates));
        });
    }

    /** @return list<array<string, mixed>> */
    private function candidates(string $tenantId, string $productId, ?string $warehouseId, StockSelectionStrategy $strategy, DateTimeImmutable $now): array
    {
        $warehouseFilter = $warehouseId === null ? '' : 'AND l.warehouse_id = :warehouseId ';
        $order = $strategy->orderByClause();
        $parameters = ['tenantId' => $tenantId, 'productId' => $productId, 'today' => $now->format('Y-m-d')];
        if ($warehouseId !== null) {
            $parameters['warehouseId'] = $warehouseId;
        }

        return $this->connection->fetchAllAssociative(
            'SELECT b.location_id, b.stock_key, b.stock_status, b.batch_number, b.serial_number, b.expires_at, '
            . 'b.quantity - COALESCE(SUM(a.quantity), 0) available_quantity, '
            . '(SELECT MIN(e.occurred_at) FROM wms_stock_ledger e WHERE e.tenant_id = b.tenant_id '
            . 'AND e.product_id = b.product_id AND e.location_id = b.location_id AND e.stock_key = b.stock_key AND e.quantity_delta > 0) first_received_at '
            . 'FROM wms_stock_balance b INNER JOIN wms_storage_location l ON l.id = b.location_id '
            . "LEFT JOIN wms_stock_allocation a ON a.tenant_id = b.tenant_id AND a.product_id = b.product_id AND a.location_id = b.location_id AND a.stock_key = b.stock_key AND a.status = 'active' "
            . 'LEFT JOIN wms_stock_classification c ON c.tenant_id = b.tenant_id AND c.product_id = b.product_id AND c.location_id = b.location_id AND c.stock_key = b.stock_key '
            . 'LEFT JOIN wms_special_stock_type t ON t.id = c.special_stock_type_id '
            . "WHERE b.tenant_id = :tenantId AND b.product_id = :productId AND b.stock_status = 'available' "
            . 'AND (t.id IS NULL OR t.allocatable = 1) AND (b.expires_at IS NULL OR b.expires_at >= :today) '
            . $warehouseFilter
            . 'GROUP BY b.location_id, b.stock_key, b.stock_status, b.batch_number, b.serial_number, b.expires_at, b.quantity '
            . 'HAVING available_quantity > 0 ORDER BY ' . $order . ', b.location_id, b.stock_key FOR UPDATE',
            $parameters,
        );
    }

    private function assertTenantReference(string $table, string $id, string $tenantId): void
    {
        if ($this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id AND tenant_id = :tenantId', $table), ['id' => $id, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('A referenced stock selection resource does not exist in the tenant.');
        }
    }

    /** @param array<string, mixed> $row */
    private function string(array $row, string $field): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new \UnexpectedValueException(sprintf('Expected field "%s" to be a non-empty string.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $row */
    private function nullableString(array $row, string $field): ?string
    {
        $value = $row[$field] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** @param array<string, mixed> $row */
    private function integer(array $row, string $field): int
    {
        $value = $row[$field] ?? null;
        if (!is_int($value) && !is_string($value)) {
            throw new \UnexpectedValueException(sprintf('Expected field "%s" to be numeric.', $field));
        }
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new \UnexpectedValueException(sprintf('Expected field "%s" to be numeric.', $field));
        }

        return (int) $value;
    }
}
