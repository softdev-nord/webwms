<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class InboundResult
{
    public function __construct(public string $deliveryStatus, public string $lineStatus, public ?string $stockStatus = null, public ?int $resultingQuantity = null) {}
}
