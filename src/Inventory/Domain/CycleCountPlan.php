<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class CycleCountPlan
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $warehouseId,
        private string $code,
        private string $locationPrefix,
        private int $intervalDays,
        private DateTimeImmutable $nextDueAt,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        if (trim($code) === '' || mb_strlen($code) > 50) {
            throw new InvalidArgumentException('A cycle count plan code must contain 1 to 50 characters.');
        }
        if (preg_match('/^[A-Z0-9][A-Z0-9._-]{0,49}$/', $locationPrefix) !== 1) {
            throw new InvalidArgumentException('The cycle count location prefix must use uppercase location characters.');
        }
        if ($intervalDays < 1 || $intervalDays > 365) {
            throw new InvalidArgumentException('A cycle count interval must contain 1 to 365 days.');
        }
        if ($nextDueAt < $createdAt) {
            throw new InvalidArgumentException('The first cycle count due date must not precede creation.');
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

    public function code(): string
    {
        return mb_strtoupper(trim($this->code));
    }

    public function locationPrefix(): string
    {
        return $this->locationPrefix;
    }

    public function intervalDays(): int
    {
        return $this->intervalDays;
    }

    public function nextDueAt(): DateTimeImmutable
    {
        return $this->nextDueAt;
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
