<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateStorageLocationCommand
{
    public function __construct(
        public string $locationId,
        public string $tenantId,
        public string $warehouseId,
        public string $code,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
