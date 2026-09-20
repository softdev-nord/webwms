<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use WebWMS\Integration\Domain\Device;
use WebWMS\Integration\Domain\DeviceNotFoundException;
use WebWMS\Integration\Domain\DeviceRepository;
use WebWMS\Integration\Domain\ScanEvent;

final readonly class DbalDeviceRepository implements DeviceRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function addDevice(Device $device): void
    {
        $this->assertActor($device->tenantId, $device->createdBy);
        $this->connection->insert('wms_device', [
            'id' => $device->id, 'tenant_id' => $device->tenantId, 'code' => $device->code,
            'name' => $device->name, 'device_type' => $device->type, 'active' => $device->active ? 1 : 0,
            'created_by' => $device->createdBy, 'created_at' => $this->date($device->createdAt),
            'changed_by' => null, 'changed_at' => null,
        ]);
    }

    public function device(string $tenantId, string $deviceId, bool $activeOnly = false): Device
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, code, name, device_type, active, created_by, created_at FROM wms_device '
            . 'WHERE tenant_id = :tenantId AND id = :id' . ($activeOnly ? ' AND active = 1' : ''),
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );
        if ($row === false) {
            throw new DeviceNotFoundException('The device does not exist or is inactive in the tenant.');
        }

        return new Device(
            (string) $row['id'], (string) $row['tenant_id'], (string) $row['code'], (string) $row['name'],
            (string) $row['device_type'], (bool) $row['active'], (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
        );
    }

    public function changeStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->update('wms_device', [
            'active' => $active ? 1 : 0,
            'changed_by' => $actorId,
            'changed_at' => $this->date($at),
        ], ['tenant_id' => $tenantId, 'id' => $deviceId]) !== 1) {
            throw new DeviceNotFoundException('The device does not exist in the tenant.');
        }
    }

    public function addScan(ScanEvent $event): ScanEvent
    {
        $this->assertActor($event->tenantId, $event->scannedBy);
        try {
            $this->connection->insert('wms_scan_event', [
                'id' => $event->id, 'tenant_id' => $event->tenantId, 'device_id' => $event->deviceId,
                'scan_type' => $event->scanType, 'scan_value' => $event->value,
                'process_type' => $event->processType, 'context_reference' => $event->contextReference,
                'request_id' => $event->requestId, 'status' => $event->status, 'message' => $event->message,
                'scanned_by' => $event->scannedBy, 'scanned_at' => $this->date($event->scannedAt),
            ]);
        } catch (UniqueConstraintViolationException) {
            $existing = $this->scanByRequestId($event->tenantId, $event->requestId);
            if ($existing === null) {
                throw new \LogicException('The idempotent scan event could not be resolved.');
            }

            return $existing;
        }

        return $event;
    }

    public function scanByRequestId(string $tenantId, string $requestId): ?ScanEvent
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM wms_scan_event WHERE tenant_id = :tenantId AND request_id = :requestId',
            ['tenantId' => $tenantId, 'requestId' => $requestId],
        );

        return $row === false ? null : $this->hydrateScan($row);
    }

    /** @param array<string, mixed> $row */
    private function hydrateScan(array $row): ScanEvent
    {
        return new ScanEvent(
            (string) $row['id'], (string) $row['tenant_id'], (string) $row['device_id'],
            (string) $row['scan_type'], (string) $row['scan_value'], (string) $row['process_type'],
            (string) $row['context_reference'], (string) $row['request_id'], (string) $row['status'],
            is_string($row['message']) ? $row['message'] : null, (string) $row['scanned_by'],
            new DateTimeImmutable((string) $row['scanned_at']),
        );
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId',
            ['id' => $actorId, 'tenantId' => $tenantId],
        ) === false) {
            throw new DeviceNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
