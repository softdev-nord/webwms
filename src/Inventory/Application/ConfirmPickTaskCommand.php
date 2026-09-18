<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ConfirmPickTaskCommand
{
    public function __construct(
        public string $taskId,
        public string $tenantId,
        public string $outcome,
        public ?string $ledgerEntryId,
        public string $note,
        public string $confirmedBy,
        public DateTimeImmutable $confirmedAt
    ) {
    }
}
