<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class StockAllocationTransition
{
    public function __construct(
        private InventoryId $allocationId,
        private TenantId $tenantId,
        private AllocationTransitionType $type,
        private ?InventoryId $ledgerEntryId,
        private string $reason,
        private UserId $performedBy,
        private DateTimeImmutable $occurredAt,
    ) {
        if ($type === AllocationTransitionType::Consume && $ledgerEntryId === null) {
            throw new InvalidArgumentException('Consumption requires a stock ledger entry ID.');
        }
        if (trim($reason) === '' || mb_strlen($reason) > 255) {
            throw new InvalidArgumentException('An allocation transition reason must contain 1 to 255 characters.');
        }
    }

    public function allocationId(): InventoryId
    {
        return $this->allocationId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function type(): AllocationTransitionType
    {
        return $this->type;
    }

    public function ledgerEntryId(): ?InventoryId
    {
        return $this->ledgerEntryId;
    }

    public function reason(): string
    {
        return trim($this->reason);
    }

    public function performedBy(): UserId
    {
        return $this->performedBy;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
