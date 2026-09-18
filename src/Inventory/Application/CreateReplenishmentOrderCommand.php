<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateReplenishmentOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $tenantId,
        public string $policyId,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
