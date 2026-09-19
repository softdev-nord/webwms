<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use WebWMS\Integration\Domain\IntegrationStatusEvent;
use WebWMS\Integration\Domain\OutboxAcknowledgement;
use WebWMS\Integration\Domain\OutboxMessageNotFoundException;
use WebWMS\Integration\Domain\OutboxRepository;

final readonly class DbalOutboxRepository implements OutboxRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function append(IntegrationStatusEvent $event): void
    {
        $this->connection->insert('wms_integration_outbox', [
            'id' => $event->id,
            'tenant_id' => $event->tenantId,
            'event_name' => $event->eventName,
            'aggregate_type' => $event->aggregateType,
            'aggregate_id' => $event->aggregateId,
            'payload' => json_encode($event->payload, JSON_THROW_ON_ERROR),
            'status' => 'pending',
            'occurred_at' => $event->occurredAt->format('Y-m-d H:i:s.u'),
            'created_by' => $event->createdBy,
            'acknowledged_by' => null,
            'acknowledged_at' => null,
        ]);
    }

    public function acknowledge(OutboxAcknowledgement $acknowledgement): void
    {
        $this->connection->transactional(function (Connection $connection) use ($acknowledgement): void {
            $message = $connection->fetchAssociative(
                'SELECT status FROM wms_integration_outbox WHERE id = :id AND tenant_id = :tenantId FOR UPDATE',
                ['id' => $acknowledgement->messageId, 'tenantId' => $acknowledgement->tenantId],
            );
            $userExists = $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $acknowledgement->acknowledgedBy, 'tenantId' => $acknowledgement->tenantId],
            );
            if ($message === false || $userExists === false) {
                throw new OutboxMessageNotFoundException('An outbox message and acknowledging user must exist in the tenant.');
            }
            if ($message['status'] === 'acknowledged') {
                return;
            }
            $connection->update('wms_integration_outbox', [
                'status' => 'acknowledged',
                'acknowledged_by' => $acknowledgement->acknowledgedBy,
                'acknowledged_at' => $acknowledgement->acknowledgedAt->format('Y-m-d H:i:s.u'),
            ], ['id' => $acknowledgement->messageId, 'tenant_id' => $acknowledgement->tenantId]);
        });
    }
}
