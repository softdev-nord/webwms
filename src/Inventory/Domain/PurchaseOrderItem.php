<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class PurchaseOrderItem
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $productId,
        private int $orderedQuantity
    ) {
        if ($orderedQuantity < 1) {
            throw new InvalidArgumentException('An ordered quantity must be positive.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function productId(): InventoryId
    {
        return $this->productId;
    }

    public function orderedQuantity(): int
    {
        return $this->orderedQuantity;
    }
}
