<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class TransferStockCommand
{
    public function __construct(
        public string $transferId,
        public string $sourcePostingId,
        public string $destinationPostingId,
        public string $tenantId,
        public string $productId,
        public string $sourceLocationId,
        public string $destinationLocationId,
        public int $quantity,
        public string $reason,
        public string $performedBy,
        public DateTimeImmutable $occurredAt,
        public string $sourceStatus = 'available',
        public string $destinationStatus = 'available',
        public ?string $batchNumber = null,
        public ?string $serialNumber = null,
        public ?DateTimeImmutable $expiresAt = null,
    ) {
    }
}
