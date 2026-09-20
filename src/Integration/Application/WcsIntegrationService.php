<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\MachineCommand;
use WebWMS\Integration\Domain\MachineStatus;
use WebWMS\Integration\Domain\WcsConnection;
use WebWMS\Integration\Domain\WcsRepository;

final readonly class WcsIntegrationService
{
    public function __construct(
        private WcsRepository $repository,
    ) {
    }

    public function registerConnection(string $tenantId, string $code, string $name, string $systemType, string $endpointUrl, string $credentialEnv, bool $active, string $actorId, DateTimeImmutable $at): WcsConnection
    {
        $connection = new WcsConnection(Uuid::v7()->toRfc4122(), $tenantId, mb_strtoupper(trim($code)), trim($name), $systemType, trim($endpointUrl), mb_strtoupper(trim($credentialEnv)), $active, $actorId, $at);
        $this->repository->addConnection($connection);

        return $connection;
    }

    public function changeConnectionStatus(string $tenantId, string $connectionId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->repository->changeConnectionStatus($tenantId, $connectionId, $active, $actorId, $at);
    }

    public function queueCommand(string $tenantId, string $connectionId, string $commandType, string $source, string $destination, string $loadUnit, string $requestId, string $actorId, DateTimeImmutable $at): MachineCommand
    {
        $existing = $this->repository->commandByRequestId($tenantId, trim($requestId));
        if ($existing !== null) {
            return $existing;
        }
        $this->repository->connection($tenantId, $connectionId, true);

        return $this->repository->addCommand(new MachineCommand(Uuid::v7()->toRfc4122(), $tenantId, $connectionId, $commandType, trim($source), trim($destination), trim($loadUnit), trim($requestId), MachineCommand::STATUS_QUEUED, null, $actorId, $at));
    }

    public function transitionCommand(string $tenantId, string $commandId, string $status, ?string $message, string $actorId, DateTimeImmutable $at): MachineCommand
    {
        return $this->repository->transitionCommand($tenantId, $commandId, $status, $this->message($message), $actorId, $at);
    }

    public function recordStatus(string $tenantId, string $connectionId, ?string $commandId, string $machineCode, string $status, ?string $message, string $externalEventId, string $actorId, DateTimeImmutable $at): MachineStatus
    {
        $existing = $this->repository->statusByExternalEventId($tenantId, trim($externalEventId));
        if ($existing !== null) {
            return $existing;
        }
        $this->repository->connection($tenantId, $connectionId, true);

        return $this->repository->addMachineStatus(new MachineStatus(Uuid::v7()->toRfc4122(), $tenantId, $connectionId, $commandId, mb_strtoupper(trim($machineCode)), $status, $this->message($message), trim($externalEventId), $actorId, $at));
    }

    private function message(?string $message): ?string
    {
        return $message === null || trim($message) === '' ? null : trim($message);
    }
}
