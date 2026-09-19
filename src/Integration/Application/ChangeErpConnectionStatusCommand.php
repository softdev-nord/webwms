<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;

final readonly class ChangeErpConnectionStatusCommand
{
    public function __construct(
        public string $connectionId,
        public string $tenantId,
        public bool $active,
        public string $changedBy,
        public DateTimeImmutable $changedAt
    ) {
    }
}
