<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class MachineStatus
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $connectionId,
        public ?string $commandId,
        public string $machineCode,
        public string $status,
        public ?string $message,
        public string $externalEventId,
        public string $recordedBy,
        public DateTimeImmutable $recordedAt,
    ) {
        foreach ([$id, $tenantId, $connectionId, $machineCode, $externalEventId, $recordedBy] as $value) {
            if (trim($value) === '') {
                throw new \InvalidArgumentException('A machine status requires complete identifiers.');
            }
        }
        if (!in_array($status, ['ready', 'busy', 'blocked', 'fault', 'offline'], true)) {
            throw new \InvalidArgumentException('The machine status is unsupported.');
        }
        if ($message !== null && mb_strlen($message) > 500) {
            throw new \InvalidArgumentException('A machine status message must not exceed 500 characters.');
        }
    }
}
