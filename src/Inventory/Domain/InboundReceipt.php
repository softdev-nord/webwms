<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InboundReceipt
{
    public function __construct(private InventoryId $id, private TenantId $tenantId, private InventoryId $deliveryId, private InventoryId $deliveryLineId, private UserId $receivedBy, private DateTimeImmutable $receivedAt) {}
    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function deliveryId(): InventoryId { return $this->deliveryId; }
    public function deliveryLineId(): InventoryId { return $this->deliveryLineId; }
    public function receivedBy(): UserId { return $this->receivedBy; }
    public function receivedAt(): DateTimeImmutable { return $this->receivedAt; }
}
