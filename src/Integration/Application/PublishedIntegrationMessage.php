<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

final readonly class PublishedIntegrationMessage
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        public string $messageId,
        public string $tenantId,
        public string $eventName,
        public string $aggregateType,
        public string $aggregateId,
        public array $payload,
        public string $occurredAt
    ) {
    }
}
