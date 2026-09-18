<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class StartDueCycleCountCommand
{
    public function __construct(
        public string $planId,
        public string $countId,
        public string $tenantId,
        public string $countCode,
        public string $startedBy,
        public DateTimeImmutable $startedAt
    ) {
    }
}
