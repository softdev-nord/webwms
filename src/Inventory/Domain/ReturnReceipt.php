<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ReturnReceipt
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $returnOrderId,
        private InventoryId $returnItemId,
        private UserId $receivedBy,
        private DateTimeImmutable $receivedAt
    ) {
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function returnOrderId(): InventoryId
    {
        return $this->returnOrderId;
    }

    public function returnItemId(): InventoryId
    {
        return $this->returnItemId;
    }

    public function receivedBy(): UserId
    {
        return $this->receivedBy;
    }

    public function receivedAt(): DateTimeImmutable
    {
        return $this->receivedAt;
    }
}
