<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateCycleCountPlanCommand
{
    public function __construct(
        public string $planId,
        public string $tenantId,
        public string $warehouseId,
        public string $code,
        public string $locationPrefix,
        public int $intervalDays,
        public DateTimeImmutable $nextDueAt,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
