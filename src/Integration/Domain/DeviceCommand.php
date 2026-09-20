<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class DeviceCommand
{
    public const string STATUS_QUEUED = 'queued';
    public const string STATUS_DISPATCHED = 'dispatched';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_FAILED = 'failed';

    public function __construct(
        public string $id,
        public string $tenantId,
        public string $deviceId,
        public string $commandType,
        public string $locationId,
        public string $referenceType,
        public string $referenceId,
        public string $requestId,
        public string $status,
        public ?string $message,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public ?string $changedBy = null,
        public ?DateTimeImmutable $changedAt = null,
    ) {
        foreach ([$id, $tenantId, $deviceId, $locationId, $referenceId, $requestId, $createdBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A device command requires complete identifiers.');
            }
        }
        if (!in_array($commandType, ['present', 'store', 'retrieve'], true)) {
            throw new \InvalidArgumentException('The automation command type is unsupported.');
        }
        if (!in_array($referenceType, ['pick_task', 'putaway_order', 'replenishment_order', 'manual'], true)) {
            throw new \InvalidArgumentException('The automation reference type is unsupported.');
        }
        if (!in_array($status, [self::STATUS_QUEUED, self::STATUS_DISPATCHED, self::STATUS_COMPLETED, self::STATUS_FAILED], true)) {
            throw new \InvalidArgumentException('The automation command status is unsupported.');
        }
        if ($message !== null && mb_strlen($message) > 500) {
            throw new \InvalidArgumentException('An automation command message must not exceed 500 characters.');
        }
    }
}
