<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InboundReceipt
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $deliveryId,
        private InventoryId $deliveryLineId,
        private UserId $receivedBy,
        private DateTimeImmutable $receivedAt,
        private ?int $actualQuantity = null,
        private ?string $discrepancyReason = null,
    ) {
        if ($actualQuantity !== null && $actualQuantity < 1) {
            throw new \InvalidArgumentException('The actual inbound quantity must be positive.');
        }
        if ($discrepancyReason !== null && mb_strlen(trim($discrepancyReason)) > 255) {
            throw new \InvalidArgumentException('The discrepancy reason must not exceed 255 characters.');
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

    public function deliveryId(): InventoryId
    {
        return $this->deliveryId;
    }

    public function deliveryLineId(): InventoryId
    {
        return $this->deliveryLineId;
    }

    public function receivedBy(): UserId
    {
        return $this->receivedBy;
    }

    public function receivedAt(): DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function actualQuantity(int $advisedQuantity): int
    {
        return $this->actualQuantity ?? $advisedQuantity;
    }

    public function discrepancyReason(): ?string
    {
        $reason = $this->discrepancyReason === null ? null : trim($this->discrepancyReason);

        return $reason === '' ? null : $reason;
    }
}
