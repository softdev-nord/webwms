<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class PostStockCommand
{
    public function __construct(
        public string $postingId,
        public string $tenantId,
        public string $productId,
        public string $locationId,
        public int $quantityDelta,
        public string $reason,
        public string $performedBy,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
