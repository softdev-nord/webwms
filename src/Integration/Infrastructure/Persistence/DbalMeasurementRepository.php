<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use WebWMS\Integration\Domain\Measurement;
use WebWMS\Integration\Domain\MeasurementDevice;
use WebWMS\Integration\Domain\MeasurementDeviceNotFoundException;
use WebWMS\Integration\Domain\MeasurementRepository;

final readonly class DbalMeasurementRepository implements MeasurementRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function addDevice(MeasurementDevice $device): void
    {
        $this->assertActor($device->tenantId, $device->createdBy);
        $this->connection->insert('wms_measurement_device', [
            'id' => $device->id,
            'tenant_id' => $device->tenantId,
            'code' => $device->code,
            'name' => $device->name,
            'device_type' => $device->type,
            'active' => $device->active ? 1 : 0,
            'created_by' => $device->createdBy,
            'created_at' => $this->date($device->createdAt),
            'changed_by' => null,
            'changed_at' => null,
        ]);
    }

    public function device(string $tenantId, string $deviceId, bool $activeOnly = false): MeasurementDevice
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, code, name, device_type, active, created_by, created_at '
            . 'FROM wms_measurement_device WHERE tenant_id = :tenantId AND id = :id'
            . ($activeOnly ? ' AND active = 1' : ''),
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );
        if ($row === false) {
            throw new MeasurementDeviceNotFoundException('The measurement device does not exist or is inactive in the tenant.');
        }

        return new MeasurementDevice(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['code'],
            (string) $row['name'],
            (string) $row['device_type'],
            (bool) $row['active'],
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['created_at']),
        );
    }

    public function changeStatus(string $tenantId, string $deviceId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->update('wms_measurement_device', [
            'active' => $active ? 1 : 0,
            'changed_by' => $actorId,
            'changed_at' => $this->date($at),
        ], ['tenant_id' => $tenantId, 'id' => $deviceId]) !== 1) {
            throw new MeasurementDeviceNotFoundException('The measurement device does not exist in the tenant.');
        }
    }

    public function addMeasurement(Measurement $measurement): Measurement
    {
        return $this->connection->transactional(function (Connection $connection) use ($measurement): Measurement {
            $this->assertActor($measurement->tenantId, $measurement->measuredBy);
            if ($measurement->status === 'accepted') {
                $this->applyToTarget($connection, $measurement);
            }

            try {
                $connection->insert('wms_measurement', [
                    'id' => $measurement->id,
                    'tenant_id' => $measurement->tenantId,
                    'device_id' => $measurement->deviceId,
                    'target_type' => $measurement->targetType,
                    'target_id' => $measurement->targetId,
                    'weight_grams' => $measurement->weightGrams,
                    'length_mm' => $measurement->lengthMillimeters,
                    'width_mm' => $measurement->widthMillimeters,
                    'height_mm' => $measurement->heightMillimeters,
                    'request_id' => $measurement->requestId,
                    'status' => $measurement->status,
                    'message' => $measurement->message,
                    'measured_by' => $measurement->measuredBy,
                    'measured_at' => $this->date($measurement->measuredAt),
                ]);
            } catch (UniqueConstraintViolationException) {
                $existing = $this->measurementByRequestId($measurement->tenantId, $measurement->requestId);
                if ($existing === null) {
                    throw new \LogicException('The idempotent measurement could not be resolved.');
                }

                return $existing;
            }

            return $measurement;
        });
    }

    public function measurementByRequestId(string $tenantId, string $requestId): ?Measurement
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM wms_measurement WHERE tenant_id = :tenantId AND request_id = :requestId',
            ['tenantId' => $tenantId, 'requestId' => $requestId],
        );

        return $row === false ? null : $this->hydrate($row);
    }

    private function applyToTarget(Connection $connection, Measurement $measurement): void
    {
        $values = array_filter([
            'weight_grams' => $measurement->weightGrams,
            'length_mm' => $measurement->lengthMillimeters,
            'width_mm' => $measurement->widthMillimeters,
            'height_mm' => $measurement->heightMillimeters,
        ], static fn (?int $value): bool => $value !== null);
        if ($measurement->targetType === 'product') {
            $updated = $connection->update(
                'wms_product_reference',
                $values,
                ['id' => $measurement->targetId, 'tenant_id' => $measurement->tenantId],
            );
        } else {
            $package = $connection->fetchOne(
                'SELECT 1 FROM wms_package p INNER JOIN wms_packing_order o ON o.id = p.packing_order_id '
                . 'WHERE p.id = :id AND o.tenant_id = :tenantId AND o.status IN (:open, :packing) FOR UPDATE',
                ['id' => $measurement->targetId, 'tenantId' => $measurement->tenantId, 'open' => 'open', 'packing' => 'packing'],
            );
            $updated = $package === false ? 0 : $connection->update('wms_package', $values, ['id' => $measurement->targetId]);
        }
        if ($updated !== 1) {
            throw new MeasurementDeviceNotFoundException('The measurable target does not exist or cannot be changed in the tenant.');
        }
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Measurement
    {
        return new Measurement(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['device_id'],
            (string) $row['target_type'],
            (string) $row['target_id'],
            is_numeric($row['weight_grams']) ? (int) $row['weight_grams'] : null,
            is_numeric($row['length_mm']) ? (int) $row['length_mm'] : null,
            is_numeric($row['width_mm']) ? (int) $row['width_mm'] : null,
            is_numeric($row['height_mm']) ? (int) $row['height_mm'] : null,
            (string) $row['request_id'],
            (string) $row['status'],
            is_string($row['message']) ? $row['message'] : null,
            (string) $row['measured_by'],
            new DateTimeImmutable((string) $row['measured_at']),
        );
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId',
            ['id' => $actorId, 'tenantId' => $tenantId],
        ) === false) {
            throw new MeasurementDeviceNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
