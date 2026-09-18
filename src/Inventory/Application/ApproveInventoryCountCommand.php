<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ApproveInventoryCountCommand
{
    /** @param array<string, string> $ledgerEntryIds */
    public function __construct(
        public string $countId,
        public string $tenantId,
        public array $ledgerEntryIds,
        public string $approvedBy,
        public DateTimeImmutable $approvedAt
    ) {
    }
}
