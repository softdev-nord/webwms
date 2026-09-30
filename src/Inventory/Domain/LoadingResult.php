<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

readonly class LoadingResult
{
    public function __construct(
        public string $status,
        public int $loadedShipments,
        public int $totalShipments
    ) {
    }
}
