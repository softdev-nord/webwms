<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class DispatchShipmentCommand
{
    public function __construct(
        public string $shipmentId,
        public string $tenantId,
        public string $handoverReference,
        public string $dispatchedBy,
        public DateTimeImmutable $dispatchedAt
    ) {
    }
}
