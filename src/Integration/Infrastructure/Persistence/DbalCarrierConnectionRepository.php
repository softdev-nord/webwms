<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use WebWMS\Integration\Domain\CarrierConnection;
use WebWMS\Integration\Domain\CarrierConnectionNotFoundException;
use WebWMS\Integration\Domain\CarrierConnectionRepository;

final readonly class DbalCarrierConnectionRepository implements CarrierConnectionRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function add(CarrierConnection $connection): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_tenant t, wms_user_account u WHERE t.id = :tenantId AND u.id = :userId AND u.tenant_id = t.id',
            ['tenantId' => $connection->tenantId, 'userId' => $connection->createdBy],
        ) === false) {
            throw new CarrierConnectionNotFoundException('The carrier connection tenant and creating user must exist.');
        }
        $this->connection->insert('wms_carrier_connection', [
            'id' => $connection->id, 'tenant_id' => $connection->tenantId, 'name' => $connection->name,
            'carrier_code' => $connection->carrierCode, 'endpoint_url' => $connection->endpointUrl,
            'credential_env' => $connection->credentialEnv, 'active' => $connection->active ? 1 : 0,
            'created_by' => $connection->createdBy, 'created_at' => $connection->createdAt->format('Y-m-d H:i:s.u'),
            'changed_by' => null, 'changed_at' => null,
        ]);
    }

    public function active(string $tenantId, string $carrierCode): CarrierConnection
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, name, carrier_code, endpoint_url, credential_env, active, created_by, created_at '
            . 'FROM wms_carrier_connection WHERE tenant_id = :tenantId AND carrier_code = :carrierCode AND active = 1',
            ['tenantId' => $tenantId, 'carrierCode' => mb_strtoupper($carrierCode)],
        );
        if ($row === false) {
            throw new CarrierConnectionNotFoundException('No active carrier connection exists for this tenant and carrier.');
        }

        return $this->hydrate($row);
    }

    public function changeStatus(string $id, string $tenantId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        if ($this->connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId', ['id' => $actorId, 'tenantId' => $tenantId]) === false) {
            throw new CarrierConnectionNotFoundException('The acting user must exist in the tenant.');
        }
        if ($this->connection->update('wms_carrier_connection', [
            'active' => $active ? 1 : 0, 'changed_by' => $actorId, 'changed_at' => $at->format('Y-m-d H:i:s.u'),
        ], ['id' => $id, 'tenant_id' => $tenantId]) !== 1) {
            throw new CarrierConnectionNotFoundException('The carrier connection does not exist in the tenant.');
        }
    }

    public function successfulRequest(string $tenantId, string $idempotencyKey, string $operation, string $aggregateId): ?array
    {
        $payload = $this->connection->fetchOne(
            "SELECT response_payload FROM wms_carrier_request WHERE tenant_id = :tenantId AND idempotency_key = :requestId AND operation = :operation AND aggregate_id = :aggregateId AND status = 'succeeded'",
            ['tenantId' => $tenantId, 'requestId' => $idempotencyKey, 'operation' => $operation, 'aggregateId' => $aggregateId],
        );
        if (!is_string($payload)) {
            return null;
        }
        $response = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($response)) {
            throw new \LogicException('The persisted carrier response is invalid.');
        }

        return $response;
    }

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
    ): void {
        $this->connection->insert('wms_carrier_request', [
            'id' => $id, 'tenant_id' => $tenantId, 'connection_id' => $connectionId, 'operation' => $operation,
            'idempotency_key' => $idempotencyKey, 'aggregate_type' => $aggregateType, 'aggregate_id' => $aggregateId,
            'status' => $status, 'response_payload' => json_encode($response, JSON_THROW_ON_ERROR),
            'executed_by' => $actorId, 'executed_at' => $at->format('Y-m-d H:i:s.u'),
        ]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): CarrierConnection
    {
        return new CarrierConnection((string) $row['id'], (string) $row['tenant_id'], (string) $row['name'], (string) $row['carrier_code'], (string) $row['endpoint_url'], (string) $row['credential_env'], (bool) $row['active'], (string) $row['created_by'], new DateTimeImmutable((string) $row['created_at']));
    }
}
