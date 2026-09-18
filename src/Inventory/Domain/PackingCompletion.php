<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PackingCompletion
{
    public function __construct(
        private InventoryId $packingOrderId,
        private TenantId $tenantId,
        private UserId $completedBy,
        private DateTimeImmutable $completedAt
    ) {
    }

    public function packingOrderId(): InventoryId
    {
        return $this->packingOrderId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function completedBy(): UserId
    {
        return $this->completedBy;
    }

    public function completedAt(): DateTimeImmutable
    {
        return $this->completedAt;
    }
}
