<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreatePurchaseOrderCommand
{
    /** @param list<array{id: string, productId: string, quantity: int}> $items */
    public function __construct(
        public string $purchaseOrderId,
        public string $tenantId,
        public string $code,
        public string $supplierReference,
        public array $items,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
