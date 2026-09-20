<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface DeviceRepository
{
    public function addDevice(Device $device): void;

    public function device(string $tenantId, string $deviceId, bool $activeOnly = false): Device;

    public function changeStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void;

    public function addScan(ScanEvent $event): ScanEvent;

    public function scanByRequestId(string $tenantId, string $requestId): ?ScanEvent;
}
