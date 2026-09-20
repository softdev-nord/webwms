<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\AutomationDevice;
use WebWMS\Integration\Domain\AutomationRepository;
use WebWMS\Integration\Domain\DeviceCommand;

final readonly class StorageAutomationAdapter
{
    public function __construct(
        private AutomationRepository $repository,
    ) {
    }

    public function registerDevice(
        string $tenantId,
        string $code,
        string $name,
        string $type,
        string $endpointUrl,
        string $credentialEnv,
        bool $active,
        string $actorId,
        DateTimeImmutable $at,
    ): AutomationDevice {
        $device = new AutomationDevice(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            mb_strtoupper(trim($code)),
            trim($name),
            $type,
            trim($endpointUrl),
            mb_strtoupper(trim($credentialEnv)),
            $active,
            $actorId,
            $at,
        );
        $this->repository->addDevice($device);

        return $device;
    }

    public function changeDeviceStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->repository->changeDeviceStatus($tenantId, $deviceId, $active, $actorId, $at);
    }

    public function queueCommand(
        string $tenantId,
        string $deviceId,
        string $commandType,
        string $locationId,
        string $referenceType,
        string $referenceId,
        string $requestId,
        string $actorId,
        DateTimeImmutable $at,
    ): DeviceCommand {
        $existing = $this->repository->commandByRequestId($tenantId, $requestId);
        if ($existing !== null) {
            return $existing;
        }
        $this->repository->device($tenantId, $deviceId, true);

        return $this->repository->addCommand(new DeviceCommand(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            $deviceId,
            $commandType,
            $locationId,
            $referenceType,
            $referenceId,
            trim($requestId),
            DeviceCommand::STATUS_QUEUED,
            null,
            $actorId,
            $at,
        ));
    }

    public function transition(
        string $tenantId,
        string $commandId,
        string $status,
        ?string $message,
        string $actorId,
        DateTimeImmutable $at,
    ): DeviceCommand {
        return $this->repository->transitionCommand(
            $tenantId,
            $commandId,
            $status,
            $message === null || trim($message) === '' ? null : trim($message),
            $actorId,
            $at,
        );
    }
}
