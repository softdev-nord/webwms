<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ConfirmShipmentLoadingCommand
{
    public function __construct(
        public string $manifestId,
        public string $tenantId,
        public string $shipmentId,
        public string $loadedBy,
        public DateTimeImmutable $loadedAt
    ) {
    }
}
