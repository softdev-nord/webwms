<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface OutboxRepository
{
    public function append(IntegrationStatusEvent $event): void;

    public function acknowledge(OutboxAcknowledgement $acknowledgement): void;

    /** @return list<OutboxMessage> */
    public function claimDue(int $limit, DateTimeImmutable $now, DateTimeImmutable $leaseExpiredBefore): array;

    public function markPublished(OutboxMessage $message, DateTimeImmutable $publishedAt): void;

    public function markFailed(
        OutboxMessage $message,
        string $error,
        DateTimeImmutable $failedAt,
        ?DateTimeImmutable $nextAttemptAt
    ): void;

    public function retryDeadLetter(
        string $messageId,
        string $tenantId,
        string $retriedBy,
        DateTimeImmutable $retriedAt
    ): void;
}
