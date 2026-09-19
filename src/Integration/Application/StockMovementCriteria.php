<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Inventory\Domain\StockMovementType;

final readonly class StockMovementCriteria
{
    public function __construct(
        public ?string $productId,
        public ?string $locationId,
        public ?string $transferId,
        public ?string $movementType
    ) {
        foreach ([$productId, $locationId, $transferId] as $identifier) {
            if ($identifier !== null && trim($identifier) === '') {
                throw new \InvalidArgumentException('Stock movement filter identifiers must not be blank.');
            }
        }
        if ($movementType !== null && StockMovementType::tryFrom($movementType) === null) {
            throw new \InvalidArgumentException('The stock movement type filter is invalid.');
        }
    }
}
