<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class PickConfirmationResult
{
    public function __construct(public string $taskStatus, public string $pickListStatus, public string $reservationStatus) {}
}
