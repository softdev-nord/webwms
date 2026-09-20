<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\IntegrationTransportRepository;
use WebWMS\Integration\Domain\ProtocolConfiguration;
use WebWMS\Integration\Domain\TransportEndpoint;

final readonly class IntegrationTransportService
{
    public function __construct(
        private IntegrationTransportRepository $repository,
    ) {
    }

    public function register(
        string $tenantId,
        string $code,
        string $name,
        string $adapterType,
        string $address,
        string $credentialEnv,
        string $protocol,
        string $framing,
        int $connectTimeoutMs,
        int $readTimeoutMs,
        bool $active,
        string $actorId,
        DateTimeImmutable $at,
    ): TransportEndpoint {
        $endpoint = new TransportEndpoint(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            mb_strtoupper(trim($code)),
            trim($name),
            $adapterType,
            trim($address),
            mb_strtoupper(trim($credentialEnv)),
            $active,
            $actorId,
            $at,
        );
        $this->repository->add($endpoint, new ProtocolConfiguration($endpoint->id, $protocol, $framing, $connectTimeoutMs, $readTimeoutMs));

        return $endpoint;
    }

    public function changeStatus(string $tenantId, string $endpointId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->repository->changeStatus($tenantId, $endpointId, $active, $actorId, $at);
    }
}
