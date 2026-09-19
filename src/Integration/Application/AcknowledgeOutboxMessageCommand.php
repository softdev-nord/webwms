<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;

final readonly class AcknowledgeOutboxMessageCommand
{
    public function __construct(
        public string $messageId,
        public string $tenantId,
        public string $acknowledgedBy,
        public DateTimeImmutable $acknowledgedAt
    ) {
    }
}
