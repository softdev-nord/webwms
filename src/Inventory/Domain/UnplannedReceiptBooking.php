<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class UnplannedReceiptBooking
{
    public function __construct(
        private InventoryId $receiptId,
        private TenantId $tenantId,
        private UserId $bookedBy,
        private DateTimeImmutable $bookedAt,
    ) {
    }

    public function receiptId(): InventoryId
    {
        return $this->receiptId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function bookedBy(): UserId
    {
        return $this->bookedBy;
    }

    public function bookedAt(): DateTimeImmutable
    {
        return $this->bookedAt;
    }
}
