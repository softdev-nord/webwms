<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ShipmentDispatch
{
    public function __construct(
        private InventoryId $shipmentId,
        private TenantId $tenantId,
        private string $handoverReference,
        private UserId $dispatchedBy,
        private DateTimeImmutable $dispatchedAt
    ) {
        if (trim($handoverReference) === '' || mb_strlen($handoverReference) > 100) {
            throw new InvalidArgumentException('A handover reference must contain 1 to 100 characters.');
        }
    }

    public function shipmentId(): InventoryId
    {
        return $this->shipmentId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function handoverReference(): string
    {
        return trim($this->handoverReference);
    }

    public function dispatchedBy(): UserId
    {
        return $this->dispatchedBy;
    }

    public function dispatchedAt(): DateTimeImmutable
    {
        return $this->dispatchedAt;
    }
}
