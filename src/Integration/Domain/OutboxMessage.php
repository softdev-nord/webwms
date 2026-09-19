<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class OutboxMessage
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $eventName,
        public string $aggregateType,
        public string $aggregateId,
        public array $payload,
        public string $createdBy,
        public DateTimeImmutable $occurredAt,
        public int $attemptNumber
    ) {
        if ($attemptNumber < 1) {
            throw new \InvalidArgumentException('A claimed outbox message requires a positive attempt number.');
        }
    }
}
