<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\Device;
use WebWMS\Integration\Domain\DeviceRepository;
use WebWMS\Integration\Domain\ScanEvent;

final readonly class DeviceIntegrationService
{
    public function __construct(
        private DeviceRepository $repository,
    ) {
    }

    public function registerDevice(string $tenantId, string $code, string $name, string $type, bool $active, string $actorId, DateTimeImmutable $at): Device
    {
        $device = new Device(Uuid::v7()->toRfc4122(), $tenantId, mb_strtoupper(trim($code)), trim($name), $type, $active, $actorId, $at);
        $this->repository->addDevice($device);

        return $device;
    }

    public function changeStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->repository->changeStatus($tenantId, $deviceId, $active, $actorId, $at);
    }

    public function recordScan(
        string $tenantId,
        string $deviceId,
        string $scanType,
        string $value,
        string $processType,
        string $contextReference,
        string $requestId,
        bool $accepted,
        ?string $message,
        string $actorId,
        DateTimeImmutable $at,
    ): ScanEvent {
        $existing = $this->repository->scanByRequestId($tenantId, $requestId);
        if ($existing !== null) {
            return $existing;
        }
        $this->repository->device($tenantId, $deviceId, true);
        $event = new ScanEvent(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            $deviceId,
            $scanType,
            trim($value),
            $processType,
            trim($contextReference),
            trim($requestId),
            $accepted ? ScanEvent::STATUS_ACCEPTED : ScanEvent::STATUS_REJECTED,
            $message === null || trim($message) === '' ? null : trim($message),
            $actorId,
            $at,
        );

        return $this->repository->addScan($event);
    }
}
