<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InventoryCountEntry
{
    public function __construct(private InventoryId $countId, private InventoryId $lineId, private TenantId $tenantId, private int $countedQuantity, private UserId $countedBy, private DateTimeImmutable $countedAt)
    {
        if ($countedQuantity < 0) {
            throw new InvalidArgumentException('A counted quantity must not be negative.');
        }
    }
    public function countId(): InventoryId { return $this->countId; }
    public function lineId(): InventoryId { return $this->lineId; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function countedQuantity(): int { return $this->countedQuantity; }
    public function countedBy(): UserId { return $this->countedBy; }
    public function countedAt(): DateTimeImmutable { return $this->countedAt; }
}
