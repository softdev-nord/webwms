<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class RecordInventoryCountCommand
{
    public function __construct(public string $countId, public string $lineId, public string $tenantId, public int $countedQuantity, public string $countedBy, public DateTimeImmutable $countedAt) {}
}
