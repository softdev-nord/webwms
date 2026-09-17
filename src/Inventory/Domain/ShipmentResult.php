<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class ShipmentResult
{
    public function __construct(public string $status, public string $trackingNumber)
    {
    }
}
