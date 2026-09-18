<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreatePutawayOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $tenantId,
        public string $inboundReceiptId,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
