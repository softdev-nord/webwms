<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\OutboxAcknowledgement;
use WebWMS\Integration\Domain\OutboxRepository;

final readonly class AcknowledgeOutboxMessageHandler
{
    public function __construct(
        private OutboxRepository $outbox
    ) {
    }

    public function __invoke(AcknowledgeOutboxMessageCommand $command): void
    {
        $this->outbox->acknowledge(new OutboxAcknowledgement(
            $command->messageId,
            $command->tenantId,
            $command->acknowledgedBy,
            $command->acknowledgedAt,
        ));
    }
}
