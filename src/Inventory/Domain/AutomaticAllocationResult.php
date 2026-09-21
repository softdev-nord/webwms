<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class AutomaticAllocationResult
{
    public function __construct(
        public string $strategy,
        public int $requestedQuantity,
        public int $allocatedQuantity,
        public int $remainingQuantity,
        public int $candidateCount,
    ) {
    }
}
