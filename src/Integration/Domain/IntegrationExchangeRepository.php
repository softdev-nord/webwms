<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface IntegrationExchangeRepository
{
    /** @return list<IntegrationMapping> */
    public function mappingsFor(string $tenantId, string $systemType, string $messageType): array;

    public function addJob(IntegrationExchangeJob $job): void;

    public function addMapping(IntegrationMapping $mapping): void;

    public function addCommerceConnection(CommerceConnection $connection): void;

    /** @param array<string, mixed> $payload */
    public function addChannelOrder(
        string $id,
        string $tenantId,
        string $connectionId,
        string $externalOrderId,
        array $payload,
        string $importedBy,
        \DateTimeImmutable $importedAt,
    ): string;
}
