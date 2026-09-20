<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Integration\Domain\IntegrationTransportRepository;
use WebWMS\Integration\Domain\ProtocolConfiguration;
use WebWMS\Integration\Domain\TransportEndpoint;
use WebWMS\Integration\Domain\TransportEndpointNotFoundException;

final readonly class DbalIntegrationTransportRepository implements IntegrationTransportRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function add(TransportEndpoint $endpoint, ProtocolConfiguration $configuration): void
    {
        $this->connection->transactional(function (Connection $connection) use ($endpoint, $configuration): void {
            $this->assertActor($endpoint->tenantId, $endpoint->createdBy);
            $connection->insert('wms_transport_endpoint', [
                'id' => $endpoint->id,
                'tenant_id' => $endpoint->tenantId,
                'code' => $endpoint->code,
                'name' => $endpoint->name,
                'adapter_type' => $endpoint->adapterType,
                'address' => $endpoint->address,
                'credential_env' => $endpoint->credentialEnv,
                'active' => $endpoint->active ? 1 : 0,
                'created_by' => $endpoint->createdBy,
                'created_at' => $this->date($endpoint->createdAt),
                'changed_by' => null,
                'changed_at' => null,
            ]);
            $connection->insert('wms_protocol_configuration', [
                'endpoint_id' => $configuration->endpointId,
                'protocol' => $configuration->protocol,
                'framing' => $configuration->framing,
                'connect_timeout_ms' => $configuration->connectTimeoutMs,
                'read_timeout_ms' => $configuration->readTimeoutMs,
            ]);
        });
    }

    public function changeStatus(string $tenantId, string $endpointId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->assertActor($tenantId, $actorId);
        if ($this->connection->update('wms_transport_endpoint', [
            'active' => $active ? 1 : 0,
            'changed_by' => $actorId,
            'changed_at' => $this->date($at),
        ], ['tenant_id' => $tenantId, 'id' => $endpointId]) !== 1) {
            throw new TransportEndpointNotFoundException('The transport endpoint does not exist in the tenant.');
        }
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $actorId],
        ) === false) {
            throw new TransportEndpointNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
