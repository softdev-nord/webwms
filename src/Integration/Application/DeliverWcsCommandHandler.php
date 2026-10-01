<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use WebWMS\Integration\Domain\MachineCommand;
use WebWMS\Integration\Domain\WcsRepository;

#[AsMessageHandler]
readonly class DeliverWcsCommandHandler
{
    public function __construct(
        private WcsRepository $repository,
        private WcsCommandTransport $transport,
    ) {
    }

    public function __invoke(PublishedIntegrationMessage $message): void
    {
        if ($message->eventName !== 'integration.wcs.command.queued') {
            return;
        }

        $command = $this->repository->command($message->tenantId, $message->aggregateId);
        if ($command->status !== MachineCommand::STATUS_QUEUED) {
            return;
        }

        $connection = $this->repository->connection($message->tenantId, $command->connectionId, true);
        $this->transport->deliver($connection, $message);
        $this->repository->transitionCommand(
            $message->tenantId,
            $command->id,
            MachineCommand::STATUS_DISPATCHED,
            null,
            $command->createdBy,
            new DateTimeImmutable(),
        );
    }
}
