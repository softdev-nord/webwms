<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class RegisterShipmentLabelCommand
{
    public function __construct(
        public string $shipmentId,
        public string $tenantId,
        public string $trackingNumber,
        public string $labelReference,
        public string $registeredBy,
        public DateTimeImmutable $registeredAt
    ) {
    }
}
