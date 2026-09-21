<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ReceiveInboundDeliveryCommand
{
    public function __construct(
        public string $receiptId,
        public string $tenantId,
        public string $deliveryId,
        public string $deliveryLineId,
        public string $receivedBy,
        public DateTimeImmutable $receivedAt,
        public ?int $actualQuantity = null,
        public ?string $discrepancyReason = null,
    ) {
    }
}
