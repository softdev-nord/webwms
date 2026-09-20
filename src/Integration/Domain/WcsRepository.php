<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface WcsRepository
{
    public function addConnection(WcsConnection $connection): void;

    public function connection(string $tenantId, string $connectionId, bool $activeOnly = false): WcsConnection;

    public function changeConnectionStatus(string $tenantId, string $connectionId, bool $active, string $actorId, DateTimeImmutable $at): void;

    public function addCommand(MachineCommand $command): MachineCommand;

    public function commandByRequestId(string $tenantId, string $requestId): ?MachineCommand;

    public function transitionCommand(string $tenantId, string $commandId, string $status, ?string $message, string $actorId, DateTimeImmutable $at): MachineCommand;

    public function addMachineStatus(MachineStatus $status): MachineStatus;

    public function statusByExternalEventId(string $tenantId, string $externalEventId): ?MachineStatus;
}
