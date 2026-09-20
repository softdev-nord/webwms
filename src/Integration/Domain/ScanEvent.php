<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class ScanEvent
{
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    public function __construct(
        public string $id,
        public string $tenantId,
        public string $deviceId,
        public string $scanType,
        public string $value,
        public string $processType,
        public string $contextReference,
        public string $requestId,
        public string $status,
        public ?string $message,
        public string $scannedBy,
        public DateTimeImmutable $scannedAt,
    ) {
        foreach ([$id, $tenantId, $deviceId, $contextReference, $requestId, $scannedBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A scan event requires complete identifiers and references.');
            }
        }
        if (!in_array($scanType, ['location', 'product', 'batch', 'serial', 'shipment', 'order'], true)) {
            throw new \InvalidArgumentException('The scan type is unsupported.');
        }
        if (!in_array($processType, ['inbound', 'picking', 'packing', 'shipping', 'loading', 'inventory'], true)) {
            throw new \InvalidArgumentException('The scan process is unsupported.');
        }
        if (trim($value) === '' || mb_strlen($value) > 255 || mb_strlen($contextReference) > 100 || mb_strlen($requestId) > 100) {
            throw new \InvalidArgumentException('The scan value or reference exceeds the supported length.');
        }
        if (!in_array($status, [self::STATUS_ACCEPTED, self::STATUS_REJECTED], true)) {
            throw new \InvalidArgumentException('The scan status is invalid.');
        }
        if ($message !== null && mb_strlen($message) > 500) {
            throw new \InvalidArgumentException('The scan message exceeds the supported length.');
        }
    }
}
