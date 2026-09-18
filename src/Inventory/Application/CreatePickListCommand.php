<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreatePickListCommand
{
    /** @param list<string> $allocationIds */
    public function __construct(
        public string $pickListId,
        public string $tenantId,
        public string $code,
        public array $allocationIds,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
