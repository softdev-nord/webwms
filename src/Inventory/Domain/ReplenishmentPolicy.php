<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ReplenishmentPolicy
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $warehouseId,
        private InventoryId $productId,
        private InventoryId $targetLocationId,
        private string $code,
        private string $sourceLocationPrefix,
        private int $minimumQuantity,
        private int $targetQuantity,
        private int $priority,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        if (trim($code) === '' || mb_strlen($code) > 50 || preg_match('/^[A-Z0-9][A-Z0-9._-]{0,49}$/', $sourceLocationPrefix) !== 1) {
            throw new InvalidArgumentException('Policy code and source location prefix are invalid.');
        }
        if ($minimumQuantity < 0 || $targetQuantity <= $minimumQuantity || $priority < 1) {
            throw new InvalidArgumentException('Target quantity must exceed the non-negative minimum and priority must be positive.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function warehouseId(): InventoryId
    {
        return $this->warehouseId;
    }

    public function productId(): InventoryId
    {
        return $this->productId;
    }

    public function targetLocationId(): InventoryId
    {
        return $this->targetLocationId;
    }

    public function code(): string
    {
        return mb_strtoupper(trim($this->code));
    }

    public function sourceLocationPrefix(): string
    {
        return $this->sourceLocationPrefix;
    }

    public function minimumQuantity(): int
    {
        return $this->minimumQuantity;
    }

    public function targetQuantity(): int
    {
        return $this->targetQuantity;
    }

    public function priority(): int
    {
        return $this->priority;
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
