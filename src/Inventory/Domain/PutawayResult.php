<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class PutawayResult
{
    public function __construct(public string $status, public string $targetLocationId, public int $quantity, public ?int $destinationQuantity = null) {}
}
