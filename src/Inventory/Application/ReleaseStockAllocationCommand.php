<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ReleaseStockAllocationCommand
{
    public function __construct(
        public string $allocationId,
        public string $tenantId,
        public string $reason,
        public string $performedBy,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
