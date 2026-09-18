<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

final readonly class OutboundOrderResult
{
    public function __construct(
        public string $status,
        public int $lineCount,
        public int $reservationCount = 0
    ) {
    }
}
