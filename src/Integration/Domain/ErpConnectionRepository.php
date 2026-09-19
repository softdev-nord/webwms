<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface ErpConnectionRepository
{
    public function add(ErpConnection $connection): void;

    /** @return list<ErpConnection> */
    public function activeForTenant(string $tenantId): array;

    public function changeStatus(
        string $connectionId,
        string $tenantId,
        bool $active,
        string $changedBy,
        DateTimeImmutable $changedAt
    ): void;
}
