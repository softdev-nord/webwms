<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreatePutawayStrategyCommand
{
    public function __construct(
        public string $strategyId,
        public string $tenantId,
        public string $warehouseId,
        public string $code,
        public string $stockStatus,
        public string $locationPrefix,
        public int $priority,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
