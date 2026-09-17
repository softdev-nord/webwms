<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class RegisterProductCommand
{
    public function __construct(
        public string $productId,
        public string $tenantId,
        public string $sku,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
