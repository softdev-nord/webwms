<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class StockTransfer
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $sourcePostingId,
        private InventoryId $destinationPostingId,
        private TenantId $tenantId,
        private InventoryId $productId,
        private InventoryId $sourceLocationId,
        private StockDimensions $sourceDimensions,
        private InventoryId $destinationLocationId,
        private StockDimensions $destinationDimensions,
        private int $quantity,
        private string $reason,
        private UserId $performedBy,
        private DateTimeImmutable $occurredAt,
    ) {
        if (count(array_unique([
            $id->value(),
            $sourcePostingId->value(),
            $destinationPostingId->value(),
        ])) !== 3) {
            throw new InvalidArgumentException('Transfer and ledger entry IDs must be different.');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('A stock transfer quantity must be greater than zero.');
        }

        if (trim($reason) === '' || mb_strlen($reason) > 255) {
            throw new InvalidArgumentException('A stock transfer reason must contain 1 to 255 characters.');
        }

        if ($sourceLocationId->value() === $destinationLocationId->value()
            && $sourceDimensions->key() === $destinationDimensions->key()) {
            throw new InvalidArgumentException('Source and destination stock must be different.');
        }

        if ($sourceDimensions->batchNumber() !== $destinationDimensions->batchNumber()
            || $sourceDimensions->serialNumber() !== $destinationDimensions->serialNumber()
            || $sourceDimensions->expiresAt()?->format('Y-m-d')
                !== $destinationDimensions->expiresAt()?->format('Y-m-d')) {
            throw new InvalidArgumentException('A transfer must preserve batch, serial number and expiry date.');
        }

        if ($sourceDimensions->serialNumber() !== null && $quantity !== 1) {
            throw new InvalidSerialStockException('A serial number must be transferred one unit at a time.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function sourcePosting(): StockPosting
    {
        return new StockPosting(
            $this->sourcePostingId,
            $this->tenantId,
            $this->productId,
            $this->sourceLocationId,
            -$this->quantity,
            $this->reason,
            $this->performedBy,
            $this->occurredAt,
            $this->sourceDimensions,
        );
    }

    public function destinationPosting(): StockPosting
    {
        return new StockPosting(
            $this->destinationPostingId,
            $this->tenantId,
            $this->productId,
            $this->destinationLocationId,
            $this->quantity,
            $this->reason,
            $this->performedBy,
            $this->occurredAt,
            $this->destinationDimensions,
        );
    }
}
