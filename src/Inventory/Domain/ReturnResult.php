<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class ReturnResult
{
    public function __construct(
        public string $returnStatus,
        public string $itemStatus,
        public ?string $stockStatus = null,
        public ?int $resultingQuantity = null
    ) {
    }
}
