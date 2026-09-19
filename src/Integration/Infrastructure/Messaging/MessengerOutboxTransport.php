<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Messaging;

use Symfony\Component\Messenger\MessageBusInterface;
use WebWMS\Integration\Application\PublishedIntegrationMessage;
use WebWMS\Integration\Domain\OutboxMessage;
use WebWMS\Integration\Domain\OutboxTransport;

final readonly class MessengerOutboxTransport implements OutboxTransport
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function publish(OutboxMessage $message): void
    {
        $this->messageBus->dispatch(new PublishedIntegrationMessage(
            $message->id,
            $message->tenantId,
            $message->eventName,
            $message->aggregateType,
            $message->aggregateId,
            $message->payload,
            $message->occurredAt->format(DATE_ATOM),
        ));
    }
}
