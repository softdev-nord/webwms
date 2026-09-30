<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

readonly class PackingResult
{
    public function __construct(
        public string $status,
        public int $packageCount,
        public int $totalWeightGrams
    ) {
    }
}
