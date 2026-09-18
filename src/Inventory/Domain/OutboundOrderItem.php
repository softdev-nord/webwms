<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class OutboundOrderItem
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $productId,
        private int $quantity
    ) {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('An outbound order quantity must be greater than zero.');
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

    public function quantity(): int
    {
        return $this->quantity;
    }
}
