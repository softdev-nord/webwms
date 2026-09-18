<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class ReleaseOutboundOrderCommand
{
    /** @param array<string, string> $reservationIdsByItem */
    public function __construct(
        public string $orderId,
        public string $tenantId,
        public array $reservationIdsByItem,
        public string $releasedBy,
        public DateTimeImmutable $releasedAt
    ) {
    }
}
