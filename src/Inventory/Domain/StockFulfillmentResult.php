<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class StockFulfillmentResult
{
    public function __construct(
        public string $allocationStatus,
        public string $reservationStatus,
        public int $physicalStockQuantity,
    ) {
    }
}
