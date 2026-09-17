<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class StockPosting
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $productId,
        private InventoryId $locationId,
        private int $quantityDelta,
        private string $reason,
        private UserId $performedBy,
        private DateTimeImmutable $occurredAt,
    ) {
        if ($quantityDelta === 0) {
            throw new InvalidArgumentException('A stock posting quantity must not be zero.');
        }

        if (trim($reason) === '' || mb_strlen($reason) > 255) {
            throw new InvalidArgumentException('A stock posting reason must contain 1 to 255 characters.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function productId(): InventoryId { return $this->productId; }
    public function locationId(): InventoryId { return $this->locationId; }
    public function quantityDelta(): int { return $this->quantityDelta; }
    public function reason(): string { return trim($this->reason); }
    public function performedBy(): UserId { return $this->performedBy; }
    public function occurredAt(): DateTimeImmutable { return $this->occurredAt; }
}
