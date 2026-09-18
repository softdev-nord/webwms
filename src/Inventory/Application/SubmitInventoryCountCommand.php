<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class SubmitInventoryCountCommand
{
    public function __construct(
        public string $countId,
        public string $tenantId,
        public string $submittedBy,
        public DateTimeImmutable $submittedAt
    ) {
    }
}
