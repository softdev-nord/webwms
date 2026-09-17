<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class LoadingCompletion
{
    public function __construct(private InventoryId $manifestId, private TenantId $tenantId, private UserId $completedBy, private DateTimeImmutable $completedAt) {}
    public function manifestId(): InventoryId { return $this->manifestId; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function completedBy(): UserId { return $this->completedBy; }
    public function completedAt(): DateTimeImmutable { return $this->completedAt; }
}
