<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InventoryCountSubmission
{
    public function __construct(
        private InventoryId $countId,
        private TenantId $tenantId,
        private UserId $submittedBy,
        private DateTimeImmutable $submittedAt
    ) {
    }

    public function countId(): InventoryId
    {
        return $this->countId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function submittedBy(): UserId
    {
        return $this->submittedBy;
    }

    public function submittedAt(): DateTimeImmutable
    {
        return $this->submittedAt;
    }
}
