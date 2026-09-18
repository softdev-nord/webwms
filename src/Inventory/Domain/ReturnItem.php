<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class ReturnItem
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $productId,
        private int $expectedQuantity,
        private string $reason
    ) {
        if ($expectedQuantity < 1) {
            throw new InvalidArgumentException('A return item quantity must be positive.');
        }
        if (trim($reason) === '' || mb_strlen($reason) > 255) {
            throw new InvalidArgumentException('A return reason must contain 1 to 255 characters.');
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

    public function expectedQuantity(): int
    {
        return $this->expectedQuantity;
    }

    public function reason(): string
    {
        return trim($this->reason);
    }
}
