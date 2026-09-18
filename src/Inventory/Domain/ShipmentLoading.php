<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ShipmentLoading
{
    public function __construct(
        private InventoryId $manifestId,
        private TenantId $tenantId,
        private InventoryId $shipmentId,
        private UserId $loadedBy,
        private DateTimeImmutable $loadedAt
    ) {
    }

    public function manifestId(): InventoryId
    {
        return $this->manifestId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function shipmentId(): InventoryId
    {
        return $this->shipmentId;
    }

    public function loadedBy(): UserId
    {
        return $this->loadedBy;
    }

    public function loadedAt(): DateTimeImmutable
    {
        return $this->loadedAt;
    }
}
