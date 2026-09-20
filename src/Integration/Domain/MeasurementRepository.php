<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface MeasurementRepository
{
    public function addDevice(MeasurementDevice $device): void;

    public function device(string $tenantId, string $deviceId, bool $activeOnly = false): MeasurementDevice;

    public function changeStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void;

    public function addMeasurement(Measurement $measurement): Measurement;

    public function measurementByRequestId(string $tenantId, string $requestId): ?Measurement;
}
