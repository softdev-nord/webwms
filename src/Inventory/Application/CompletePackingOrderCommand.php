<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CompletePackingOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $tenantId,
        public string $completedBy,
        public DateTimeImmutable $completedAt
    ) {
    }
}
