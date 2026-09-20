<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class UnplannedReceiptItem
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $productId,
        private InventoryId $locationId,
        private int $quantity,
        private StockDimensions $dimensions,
    ) {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('An unplanned receipt quantity must be positive.');
        }
        if ($dimensions->serialNumber() !== null && $quantity !== 1) {
            throw new InvalidSerialStockException('An unplanned serial receipt must contain exactly one unit.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function productId(): InventoryId { return $this->productId; }
    public function locationId(): InventoryId { return $this->locationId; }
    public function quantity(): int { return $this->quantity; }
    public function dimensions(): StockDimensions { return $this->dimensions; }
}
