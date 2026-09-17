<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateStockReservationCommand
{
    public function __construct(
        public string $reservationId,
        public string $tenantId,
        public string $productId,
        public string $orderReference,
        public int $quantity,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
