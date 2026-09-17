<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ConsumeStockAllocationCommand
{
    public function __construct(
        public string $allocationId,
        public string $ledgerEntryId,
        public string $tenantId,
        public string $reason,
        public string $performedBy,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
