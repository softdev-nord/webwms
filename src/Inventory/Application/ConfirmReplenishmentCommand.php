<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ConfirmReplenishmentCommand
{
    public function __construct(public string $orderId, public string $transferId, public string $sourceLedgerId, public string $destinationLedgerId, public string $tenantId, public string $confirmedBy, public DateTimeImmutable $confirmedAt) {}
}
