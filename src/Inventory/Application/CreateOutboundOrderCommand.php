<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateOutboundOrderCommand
{
    /** @param list<array{id: string, productId: string, quantity: int}> $items */
    public function __construct(
        public string $orderId,
        public string $tenantId,
        public string $orderNumber,
        public string $customerReference,
        public array $items,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
