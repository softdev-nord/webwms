<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PickListAssignment
{
    public function __construct(
        private InventoryId $pickListId,
        private TenantId $tenantId,
        private UserId $assignedTo,
        private UserId $assignedBy,
        private DateTimeImmutable $assignedAt
    ) {
    }

    public function pickListId(): InventoryId
    {
        return $this->pickListId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function assignedTo(): UserId
    {
        return $this->assignedTo;
    }

    public function assignedBy(): UserId
    {
        return $this->assignedBy;
    }

    public function assignedAt(): DateTimeImmutable
    {
        return $this->assignedAt;
    }
}
