<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateShipmentCommand
{
    public function __construct(public string $shipmentId, public string $tenantId, public string $packingOrderId, public string $shipmentNumber, public string $carrier, public string $service, public string $createdBy, public DateTimeImmutable $createdAt)
    {
    }
}
