<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

readonly class ResolveInboundDiscrepancyCommand
{
    public function __construct(
        public string $receiptId,
        public string $tenantId,
        public string $action,
        public string $note,
        public string $transferId,
        public string $sourceLedgerId,
        public string $destinationLedgerId,
        public string $resolvedBy,
        public DateTimeImmutable $resolvedAt,
    ) {
    }
}
