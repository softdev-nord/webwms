<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface AutomationRepository
{
    public function addDevice(AutomationDevice $device): void;

    public function device(string $tenantId, string $deviceId, bool $activeOnly = false): AutomationDevice;

    public function changeDeviceStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void;

    public function addCommand(DeviceCommand $command): DeviceCommand;

    public function commandByRequestId(string $tenantId, string $requestId): ?DeviceCommand;

    public function transitionCommand(string $tenantId, string $commandId, string $status, ?string $message, string $actorId, DateTimeImmutable $at): DeviceCommand;
}
