<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class InspectInboundReceiptCommand
{
    /** @param list<array{question: string, passed: bool, note: string}> $answers */
    public function __construct(
        public string $receiptId,
        public string $ledgerEntryId,
        public string $tenantId,
        public string $locationId,
        public string $decision,
        public array $answers,
        public string $inspectedBy,
        public DateTimeImmutable $inspectedAt,
        public ?string $batchNumber = null,
        public ?string $serialNumber = null,
        public ?DateTimeImmutable $expiresAt = null
    ) {
    }
}
