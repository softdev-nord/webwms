<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateWarehouseCommand
{
    public function __construct(
        public string $warehouseId,
        public string $tenantId,
        public string $siteId,
        public string $code,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
