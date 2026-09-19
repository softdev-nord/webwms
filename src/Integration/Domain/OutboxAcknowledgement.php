<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class OutboxAcknowledgement
{
    public function __construct(
        public string $messageId,
        public string $tenantId,
        public string $acknowledgedBy,
        public DateTimeImmutable $acknowledgedAt
    ) {
        foreach ([$messageId, $tenantId, $acknowledgedBy] as $value) {
            if (trim($value) === '') {
                throw new \InvalidArgumentException('An outbox acknowledgement requires complete identifiers.');
            }
        }
    }
}
