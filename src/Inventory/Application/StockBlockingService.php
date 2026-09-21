<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\StockBlockReasonDefinition;
use WebWMS\Inventory\Domain\StockBlockStatus;
use WebWMS\Inventory\Domain\StockDimensions;

final readonly class StockBlockingService
{
    public function __construct(
        private Connection $connection,
        private TransferStockHandler $transferStock,
    ) {
    }

    public function createReason(
        string $id,
        string $tenantId,
        string $code,
        string $name,
        ?string $description,
        bool $active,
        string $actorId,
        DateTimeImmutable $now,
    ): void {
        $reason = new StockBlockReasonDefinition($code, $name, $description, $active);
        $this->assertUser($tenantId, $actorId);
        $this->connection->insert('wms_stock_block_reason', [
            'id' => $id,
            'tenant_id' => $tenantId,
            'code' => $reason->code,
            'name' => $reason->name,
            'description' => $reason->description,
            'active' => $reason->active ? 1 : 0,
            'created_by' => $actorId,
            'created_at' => $this->date($now),
        ]);
    }

    public function block(
        string $id,
        string $tenantId,
        string $reasonId,
        string $productId,
        string $locationId,
        string $sourceStatus,
        ?string $batchNumber,
        ?string $serialNumber,
        ?DateTimeImmutable $expiresAt,
        int $quantity,
        string $note,
        string $actorId,
        DateTimeImmutable $now,
    ): void {
        $note = $this->note($note);
        $source = StockDimensions::fromInput($sourceStatus, $batchNumber, $serialNumber, $expiresAt);
        if ($source->status()->value === 'blocked') {
            throw new InvalidArgumentException('Already blocked stock cannot be blocked again.');
        }
        if ($quantity < 1) {
            throw new InvalidArgumentException('The blocked quantity must be positive.');
        }
        $blocked = StockDimensions::fromInput('blocked', $batchNumber, $serialNumber, $expiresAt);

        $this->connection->transactional(function () use ($id, $tenantId, $reasonId, $productId, $locationId, $source, $blocked, $quantity, $note, $actorId, $now, $batchNumber, $serialNumber, $expiresAt): void {
            if ($this->connection->fetchOne(
                'SELECT 1 FROM wms_stock_block_reason WHERE id = :reasonId AND tenant_id = :tenantId AND active = 1 FOR UPDATE',
                ['reasonId' => $reasonId, 'tenantId' => $tenantId],
            ) === false) {
                throw new InventoryReferenceNotFoundException('An active stock block reason must exist in the tenant.');
            }

            ($this->transferStock)(new TransferStockCommand(
                Uuid::v7()->toRfc4122(),
                Uuid::v7()->toRfc4122(),
                Uuid::v7()->toRfc4122(),
                $tenantId,
                $productId,
                $locationId,
                $locationId,
                $quantity,
                'Stock blocked: ' . $note,
                $actorId,
                $now,
                $source->status()->value,
                $blocked->status()->value,
                $batchNumber,
                $serialNumber,
                $expiresAt,
            ));
            $this->connection->insert('wms_stock_block', [
                'id' => $id,
                'tenant_id' => $tenantId,
                'reason_id' => $reasonId,
                'product_id' => $productId,
                'location_id' => $locationId,
                'source_stock_key' => $source->key(),
                'blocked_stock_key' => $blocked->key(),
                'original_status' => $source->status()->value,
                'batch_number' => $batchNumber,
                'serial_number' => $serialNumber,
                'expires_at' => $expiresAt?->format('Y-m-d'),
                'quantity' => $quantity,
                'note' => $note,
                'status' => StockBlockStatus::Open->value,
                'blocked_by' => $actorId,
                'blocked_at' => $this->date($now),
            ]);
            $this->event($tenantId, $id, 'blocked', $note, $actorId, $now);
        });
    }

    public function review(string $tenantId, string $blockId, string $note, string $actorId, DateTimeImmutable $now): void
    {
        $note = $this->note($note);
        $this->connection->transactional(function () use ($tenantId, $blockId, $note, $actorId, $now): void {
            $status = $this->blockStatus($tenantId, $blockId);
            if (!$status->canReview()) {
                throw new InvalidArgumentException('Only an open stock block can be reviewed.');
            }
            $this->assertUser($tenantId, $actorId);
            $this->connection->update('wms_stock_block', [
                'status' => StockBlockStatus::Reviewed->value,
                'reviewed_by' => $actorId,
                'reviewed_at' => $this->date($now),
                'review_note' => $note,
            ], ['id' => $blockId, 'tenant_id' => $tenantId]);
            $this->event($tenantId, $blockId, 'reviewed', $note, $actorId, $now);
        });
    }

    public function release(string $tenantId, string $blockId, string $note, string $actorId, DateTimeImmutable $now): void
    {
        $note = $this->note($note);
        $this->connection->transactional(function () use ($tenantId, $blockId, $note, $actorId, $now): void {
            $row = $this->connection->fetchAssociative(
                'SELECT product_id, location_id, original_status, batch_number, serial_number, expires_at, quantity, status '
                . 'FROM wms_stock_block WHERE id = :blockId AND tenant_id = :tenantId FOR UPDATE',
                ['blockId' => $blockId, 'tenantId' => $tenantId],
            );
            if ($row === false) {
                throw new InventoryReferenceNotFoundException('The stock block does not exist in the tenant.');
            }
            $status = StockBlockStatus::from($this->string($row, 'status'));
            if (!$status->canRelease()) {
                throw new InvalidArgumentException('Only a reviewed stock block can be released.');
            }
            $expiresAt = ($expiry = $this->nullableString($row, 'expires_at')) === null ? null : new DateTimeImmutable($expiry);
            ($this->transferStock)(new TransferStockCommand(
                Uuid::v7()->toRfc4122(),
                Uuid::v7()->toRfc4122(),
                Uuid::v7()->toRfc4122(),
                $tenantId,
                $this->string($row, 'product_id'),
                $this->string($row, 'location_id'),
                $this->string($row, 'location_id'),
                $this->integer($row, 'quantity'),
                'Stock released: ' . $note,
                $actorId,
                $now,
                'blocked',
                $this->string($row, 'original_status'),
                $this->nullableString($row, 'batch_number'),
                $this->nullableString($row, 'serial_number'),
                $expiresAt,
            ));
            $this->connection->update('wms_stock_block', [
                'status' => StockBlockStatus::Released->value,
                'released_by' => $actorId,
                'released_at' => $this->date($now),
            ], ['id' => $blockId, 'tenant_id' => $tenantId]);
            $this->event($tenantId, $blockId, 'released', $note, $actorId, $now);
        });
    }

    private function blockStatus(string $tenantId, string $blockId): StockBlockStatus
    {
        $value = $this->connection->fetchOne(
            'SELECT status FROM wms_stock_block WHERE id = :blockId AND tenant_id = :tenantId FOR UPDATE',
            ['blockId' => $blockId, 'tenantId' => $tenantId],
        );
        if (!is_string($value)) {
            throw new InventoryReferenceNotFoundException('The stock block does not exist in the tenant.');
        }

        return StockBlockStatus::from($value);
    }

    private function assertUser(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :actorId AND tenant_id = :tenantId', ['actorId' => $actorId, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('The acting user does not exist in the tenant.');
        }
    }

    private function event(string $tenantId, string $blockId, string $type, string $note, string $actorId, DateTimeImmutable $now): void
    {
        $this->connection->insert('wms_stock_block_event', [
            'id' => Uuid::v7()->toRfc4122(),
            'tenant_id' => $tenantId,
            'block_id' => $blockId,
            'event_type' => $type,
            'note' => $note,
            'performed_by' => $actorId,
            'occurred_at' => $this->date($now),
        ]);
    }

    private function note(string $note): string
    {
        $note = trim($note);
        if ($note === '' || mb_strlen($note) > 255) {
            throw new InvalidArgumentException('A stock block note must contain 1 to 255 characters.');
        }

        return $note;
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
        if ((!is_int($value) && !is_string($value)) || filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new \UnexpectedValueException(sprintf('Expected field "%s" to be numeric.', $field));
        }

        return (int) $value;
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
