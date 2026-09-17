<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ShipmentLabel
{
    public function __construct(private InventoryId $shipmentId, private TenantId $tenantId, private string $trackingNumber, private string $labelReference, private UserId $registeredBy, private DateTimeImmutable $registeredAt)
    {
        if (trim($trackingNumber) === '' || mb_strlen($trackingNumber) > 100) {
            throw new InvalidArgumentException('A tracking number must contain 1 to 100 characters.');
        }
        if (trim($labelReference) === '' || mb_strlen($labelReference) > 255) {
            throw new InvalidArgumentException('A label reference must contain 1 to 255 characters.');
        }
    }

    public function shipmentId(): InventoryId { return $this->shipmentId; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function trackingNumber(): string { return trim($this->trackingNumber); }
    public function labelReference(): string { return trim($this->labelReference); }
    public function registeredBy(): UserId { return $this->registeredBy; }
    public function registeredAt(): DateTimeImmutable { return $this->registeredAt; }
}
