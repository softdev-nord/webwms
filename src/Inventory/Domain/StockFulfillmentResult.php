<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

readonly class StockFulfillmentResult
{
    public function __construct(
        public string $allocationStatus,
        public string $reservationStatus,
        public int $physicalStockQuantity,
    ) {
    }
}
