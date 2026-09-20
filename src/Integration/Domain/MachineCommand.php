<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class MachineCommand
{
    public const string STATUS_QUEUED = 'queued';
    public const string STATUS_DISPATCHED = 'dispatched';
    public const string STATUS_ACCEPTED = 'accepted';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_FAILED = 'failed';
    public const string STATUS_CANCELLED = 'cancelled';

    public function __construct(
        public string $id,
        public string $tenantId,
        public string $connectionId,
        public string $commandType,
        public string $source,
        public string $destination,
        public string $loadUnit,
        public string $requestId,
        public string $status,
        public ?string $message,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
        public ?string $changedBy = null,
        public ?DateTimeImmutable $changedAt = null,
    ) {
        foreach ([$id, $tenantId, $connectionId, $source, $destination, $loadUnit, $requestId, $createdBy] as $value) {
            if (trim($value) === '') {
                throw new \InvalidArgumentException('A machine command requires complete identifiers and routing data.');
            }
        }
        if (!in_array($commandType, ['transport', 'route', 'cancel'], true)) {
            throw new \InvalidArgumentException('The machine command type is unsupported.');
        }
        if (!in_array($status, self::statuses(), true)) {
            throw new \InvalidArgumentException('The machine command status is unsupported.');
        }
        if ($message !== null && mb_strlen($message) > 500) {
            throw new \InvalidArgumentException('A machine command message must not exceed 500 characters.');
        }
    }

    /** @return list<string> */
    public static function statuses(): array
    {
        return [self::STATUS_QUEUED, self::STATUS_DISPATCHED, self::STATUS_ACCEPTED, self::STATUS_COMPLETED, self::STATUS_FAILED, self::STATUS_CANCELLED];
    }
}
