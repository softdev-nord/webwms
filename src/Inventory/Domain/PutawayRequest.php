<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PutawayRequest
{
    public function __construct(
        private InventoryId $orderId,
        private TenantId $tenantId,
        private InventoryId $inboundReceiptId,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
    }

    public function orderId(): InventoryId
    {
        return $this->orderId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function inboundReceiptId(): InventoryId
    {
        return $this->inboundReceiptId;
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
