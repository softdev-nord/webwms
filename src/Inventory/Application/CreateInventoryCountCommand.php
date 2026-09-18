<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateInventoryCountCommand
{
    public function __construct(
        public string $countId,
        public string $tenantId,
        public string $warehouseId,
        public string $code,
        public string $locationPrefix,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
