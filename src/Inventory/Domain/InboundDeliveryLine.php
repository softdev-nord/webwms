<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class InboundDeliveryLine
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $purchaseOrderItemId,
        private int $advisedQuantity
    ) {
        if ($advisedQuantity < 1) {
            throw new InvalidArgumentException('An advised quantity must be positive.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function purchaseOrderItemId(): InventoryId
    {
        return $this->purchaseOrderItemId;
    }

    public function advisedQuantity(): int
    {
        return $this->advisedQuantity;
    }
}
