<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class StockTransferResult
{
    public function __construct(
        public int $sourceQuantity,
        public int $destinationQuantity,
    ) {
    }
}
