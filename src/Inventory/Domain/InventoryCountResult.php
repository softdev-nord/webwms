<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class InventoryCountResult
{
    public function __construct(public string $status, public int $lineCount, public int $differenceCount, public int $adjustedCount = 0) {}
}
