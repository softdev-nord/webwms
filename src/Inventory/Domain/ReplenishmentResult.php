<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

readonly class ReplenishmentResult
{
    public function __construct(
        public string $status,
        public string $sourceLocationId,
        public string $targetLocationId,
        public int $quantity,
        public ?int $destinationQuantity = null
    ) {
    }
}
