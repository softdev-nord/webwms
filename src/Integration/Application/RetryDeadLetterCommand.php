<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;

final readonly class RetryDeadLetterCommand
{
    public function __construct(
        public string $messageId,
        public string $tenantId,
        public string $retriedBy,
        public DateTimeImmutable $retriedAt
    ) {
    }
}
