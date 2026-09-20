<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\Measurement;
use WebWMS\Integration\Domain\MeasurementDevice;
use WebWMS\Integration\Domain\MeasurementRepository;

final readonly class MeasurementService
{
    public function __construct(
        private MeasurementRepository $repository,
    ) {
    }

    public function registerDevice(
        string $tenantId,
        string $code,
        string $name,
        string $type,
        bool $active,
        string $actorId,
        DateTimeImmutable $at,
    ): MeasurementDevice {
        $device = new MeasurementDevice(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            mb_strtoupper(trim($code)),
            trim($name),
            $type,
            $active,
            $actorId,
            $at,
        );
        $this->repository->addDevice($device);

        return $device;
    }

    public function changeStatus(
        string $tenantId,
        string $deviceId,
        bool $active,
        string $actorId,
        DateTimeImmutable $at,
    ): void {
        $this->repository->changeStatus($tenantId, $deviceId, $active, $actorId, $at);
    }

    public function record(
        string $tenantId,
        string $deviceId,
        string $targetType,
        string $targetId,
        ?int $weightGrams,
        ?int $lengthMillimeters,
        ?int $widthMillimeters,
        ?int $heightMillimeters,
        string $requestId,
        bool $accepted,
        ?string $message,
        string $actorId,
        DateTimeImmutable $at,
    ): Measurement {
        $existing = $this->repository->measurementByRequestId($tenantId, $requestId);
        if ($existing !== null) {
            return $existing;
        }
        $device = $this->repository->device($tenantId, $deviceId, true);
        $hasWeight = $weightGrams !== null;
        $hasDimensions = $lengthMillimeters !== null || $widthMillimeters !== null || $heightMillimeters !== null;
        if (($device->type === 'scale' && $hasDimensions) || ($device->type === 'dimensioner' && $hasWeight)) {
            throw new \InvalidArgumentException('The measurement values do not match the device capability.');
        }

        return $this->repository->addMeasurement(new Measurement(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            $deviceId,
            $targetType,
            $targetId,
            $weightGrams,
            $lengthMillimeters,
            $widthMillimeters,
            $heightMillimeters,
            trim($requestId),
            $accepted ? 'accepted' : 'rejected',
            $message === null || trim($message) === '' ? null : trim($message),
            $actorId,
            $at,
        ));
    }
}
