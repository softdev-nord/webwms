<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PackingOrder
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $pickListId,
        private string $code,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        if (trim($code) === '' || mb_strlen($code) > 50) {
            throw new InvalidArgumentException('A packing order code must contain 1 to 50 characters.');
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

    public function pickListId(): InventoryId
    {
        return $this->pickListId;
    }

    public function code(): string
    {
        return mb_strtoupper(trim($this->code));
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
