<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\OutboxRepository;

final readonly class RetryDeadLetterHandler
{
    public function __construct(
        private OutboxRepository $outbox
    ) {
    }

    public function __invoke(RetryDeadLetterCommand $command): void
    {
        $this->outbox->retryDeadLetter(
            $command->messageId,
            $command->tenantId,
            $command->retriedBy,
            $command->retriedAt,
        );
    }
}
