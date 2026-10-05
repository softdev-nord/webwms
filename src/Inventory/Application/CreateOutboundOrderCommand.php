<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

readonly class CreateOutboundOrderCommand
{
    /**
     * @param array{name: string, street: string, postalCode: string, city: string, countryCode: string} $recipientAddress
     * @param list<array{id: string, productId: string, quantity: int}>                                  $items
     */
    public function __construct(
        public string $orderId,
        public string $tenantId,
        public string $orderNumber,
        public string $customerReference,
        public array $recipientAddress,
        public array $items,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
