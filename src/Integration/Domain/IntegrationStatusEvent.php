<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class IntegrationStatusEvent
{
    /** @param array<string, bool|int|string|null> $payload */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $eventName,
        public string $aggregateType,
        public string $aggregateId,
        public array $payload,
        public string $createdBy,
        public DateTimeImmutable $occurredAt
    ) {
        foreach ([$id, $tenantId, $eventName, $aggregateType, $aggregateId, $createdBy] as $value) {
            if (trim($value) === '') {
                throw new \InvalidArgumentException('An integration status event requires complete identifiers and names.');
            }
        }
        if (mb_strlen($eventName) > 100 || mb_strlen($aggregateType) > 50 || $payload === []) {
            throw new \InvalidArgumentException('An integration status event requires bounded names and a payload.');
        }
    }
}
