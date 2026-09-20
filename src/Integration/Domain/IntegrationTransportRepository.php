<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

interface IntegrationTransportRepository
{
    public function add(TransportEndpoint $endpoint, ProtocolConfiguration $configuration): void;

    public function changeStatus(string $tenantId, string $endpointId, bool $active, string $actorId, DateTimeImmutable $at): void;
}
