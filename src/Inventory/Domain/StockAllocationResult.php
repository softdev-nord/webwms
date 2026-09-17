<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class StockAllocationResult
{
    public function __construct(
        public int $allocatedQuantity,
        public int $remainingReservationQuantity,
        public int $availableStockQuantity,
    ) {
    }
}
