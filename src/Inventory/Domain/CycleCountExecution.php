<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class CycleCountExecution
{
    public function __construct(
        private InventoryId $planId,
        private InventoryId $countId,
        private TenantId $tenantId,
        private string $countCode,
        private UserId $startedBy,
        private DateTimeImmutable $startedAt
    ) {
        if (trim($countCode) === '' || mb_strlen($countCode) > 50) {
            throw new InvalidArgumentException('A cycle count code must contain 1 to 50 characters.');
        }
    }

    public function planId(): InventoryId
    {
        return $this->planId;
    }

    public function countId(): InventoryId
    {
        return $this->countId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function countCode(): string
    {
        return mb_strtoupper(trim($this->countCode));
    }

    public function startedBy(): UserId
    {
        return $this->startedBy;
    }

    public function startedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }
}
