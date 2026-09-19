<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use WebWMS\Integration\Domain\ErpConnectionRepository;
use WebWMS\Integration\Domain\ErpStatusTransport;

#[AsMessageHandler]
final readonly class DeliverErpStatusEventHandler
{
    public function __construct(
        private ErpConnectionRepository $connections,
        private ErpStatusTransport $transport
    ) {
    }

    public function __invoke(PublishedIntegrationMessage $message): void
    {
        foreach ($this->connections->activeForTenant($message->tenantId) as $connection) {
            $this->transport->deliver($connection, $message);
        }
    }
}
