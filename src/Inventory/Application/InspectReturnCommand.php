<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class InspectReturnCommand
{
    public function __construct(public string $receiptId, public string $ledgerEntryId, public string $tenantId, public string $locationId, public string $decision, public string $note, public string $inspectedBy, public DateTimeImmutable $inspectedAt, public ?string $batchNumber = null, public ?string $serialNumber = null, public ?DateTimeImmutable $expiresAt = null) {}
}
