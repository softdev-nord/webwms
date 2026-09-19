<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface CarrierConnectionRepository
{
    public function add(CarrierConnection $connection): void;

    public function active(string $tenantId, string $carrierCode): CarrierConnection;

    public function changeStatus(string $id, string $tenantId, bool $active, string $actorId, DateTimeImmutable $at): void;

    /** @return array<string, mixed>|null */
    public function successfulRequest(string $tenantId, string $idempotencyKey, string $operation, string $aggregateId): ?array;

    /** @param array<string, mixed> $response */
    public function recordRequest(
        string $id,
        string $tenantId,
        string $connectionId,
        string $operation,
        string $idempotencyKey,
        string $aggregateType,
        string $aggregateId,
        string $status,
        array $response,
        string $actorId,
        DateTimeImmutable $at
    ): void;
}
