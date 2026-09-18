<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PickList
{
    /** @param list<InventoryId> $allocationIds */
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $outboundOrderId,
        private string $code,
        private array $allocationIds,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        if (trim($code) === '' || mb_strlen($code) > 50 || $allocationIds === []) {
            throw new InvalidArgumentException('A pick list requires a code and at least one allocation.');
        }
        if (count(array_unique(array_map(static fn (InventoryId $id): string => $id->value(), $allocationIds))) !== count($allocationIds)) {
            throw new InvalidArgumentException('A pick list must not contain duplicate allocations.');
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

    public function outboundOrderId(): InventoryId
    {
        return $this->outboundOrderId;
    }

    public function code(): string
    {
        return mb_strtoupper(trim($this->code));
    }

    /** @return list<InventoryId> */
    public function allocationIds(): array
    {
        return $this->allocationIds;
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
