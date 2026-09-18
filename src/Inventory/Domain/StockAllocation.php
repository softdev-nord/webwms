<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class StockAllocation
{
    public function __construct(
        private InventoryId $id,
        private InventoryId $reservationId,
        private TenantId $tenantId,
        private InventoryId $productId,
        private InventoryId $locationId,
        private StockDimensions $dimensions,
        private int $quantity,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt,
    ) {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('An allocation quantity must be greater than zero.');
        }
        if ($dimensions->serialNumber() !== null && $quantity !== 1) {
            throw new InvalidSerialStockException('A serial number must be allocated one unit at a time.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function reservationId(): InventoryId
    {
        return $this->reservationId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function productId(): InventoryId
    {
        return $this->productId;
    }

    public function locationId(): InventoryId
    {
        return $this->locationId;
    }

    public function dimensions(): StockDimensions
    {
        return $this->dimensions;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function createdBy(): UserId
    {
        return $this->createdBy;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
