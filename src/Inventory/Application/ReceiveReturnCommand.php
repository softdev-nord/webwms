<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ReceiveReturnCommand
{
    public function __construct(
        public string $receiptId,
        public string $tenantId,
        public string $returnOrderId,
        public string $returnItemId,
        public string $receivedBy,
        public DateTimeImmutable $receivedAt
    ) {
    }
}
