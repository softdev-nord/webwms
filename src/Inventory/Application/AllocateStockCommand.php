<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class AllocateStockCommand
{
    public function __construct(
        public string $allocationId,
        public string $reservationId,
        public string $tenantId,
        public string $productId,
        public string $locationId,
        public int $quantity,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public string $status = 'available',
        public ?string $batchNumber = null,
        public ?string $serialNumber = null,
        public ?DateTimeImmutable $expiresAt = null,
    ) {
    }
}
