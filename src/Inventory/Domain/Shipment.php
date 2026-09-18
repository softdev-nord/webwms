<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class Shipment
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $packingOrderId,
        private string $shipmentNumber,
        private string $carrier,
        private string $service,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        foreach ([$shipmentNumber, $carrier, $service] as $value) {
            if (trim($value) === '' || mb_strlen($value) > 80) {
                throw new InvalidArgumentException('Shipment number, carrier and service must contain 1 to 80 characters.');
            }
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function packingOrderId(): InventoryId
    {
        return $this->packingOrderId;
    }

    public function shipmentNumber(): string
    {
        return mb_strtoupper(trim($this->shipmentNumber));
    }

    public function carrier(): string
    {
        return mb_strtoupper(trim($this->carrier));
    }

    public function service(): string
    {
        return mb_strtoupper(trim($this->service));
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
