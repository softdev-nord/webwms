<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\IntegrationStatusEvent;
use WebWMS\Integration\Domain\OutboxAcknowledgement;
use WebWMS\Integration\Domain\OutboxMessage;
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

    public function claimDue(int $limit, DateTimeImmutable $now, DateTimeImmutable $leaseExpiredBefore): array
    {
        return $this->connection->transactional(function (Connection $connection) use ($limit, $now, $leaseExpiredBefore): array {
            $rows = $connection->fetchAllAssociative(
                'SELECT id, tenant_id, event_name, aggregate_type, aggregate_id, payload, created_by, occurred_at, attempt_count '
                . 'FROM wms_integration_outbox WHERE '
                . "(status = 'pending' AND (next_attempt_at IS NULL OR next_attempt_at <= :now)) "
                . "OR (status = 'processing' AND claimed_at <= :leaseExpiredBefore) "
                . 'ORDER BY id LIMIT ' . $limit . ' FOR UPDATE',
                ['now' => $now->format('Y-m-d H:i:s.u'), 'leaseExpiredBefore' => $leaseExpiredBefore->format('Y-m-d H:i:s.u')],
            );

            $messages = [];
            foreach ($rows as $row) {
                $attemptNumber = ((int) $row['attempt_count']) + 1;
                $connection->update('wms_integration_outbox', [
                    'status' => 'processing',
                    'claimed_at' => $now->format('Y-m-d H:i:s.u'),
                    'attempt_count' => $attemptNumber,
                ], ['id' => (string) $row['id']]);
                $messages[] = $this->hydrateMessage($row, $attemptNumber);
            }

            return $messages;
        });
    }

    public function markPublished(OutboxMessage $message, DateTimeImmutable $publishedAt): void
    {
        $this->connection->transactional(function (Connection $connection) use ($message, $publishedAt): void {
            $affected = $connection->update('wms_integration_outbox', [
                'status' => 'published',
                'published_at' => $publishedAt->format('Y-m-d H:i:s.u'),
                'claimed_at' => null,
                'last_error' => null,
                'next_attempt_at' => null,
            ], ['id' => $message->id, 'tenant_id' => $message->tenantId, 'status' => 'processing']);
            if ($affected !== 1) {
                throw new OutboxMessageNotFoundException('The claimed outbox message no longer exists or changed state.');
            }
            $this->insertAttempt($connection, $message, 'published', null, $publishedAt);
        });
    }

    public function markFailed(
        OutboxMessage $message,
        string $error,
        DateTimeImmutable $failedAt,
        ?DateTimeImmutable $nextAttemptAt
    ): void {
        $this->connection->transactional(function (Connection $connection) use ($message, $error, $failedAt, $nextAttemptAt): void {
            $affected = $connection->update('wms_integration_outbox', [
                'status' => $nextAttemptAt === null ? 'dead_letter' : 'pending',
                'claimed_at' => null,
                'last_error' => $error,
                'next_attempt_at' => $nextAttemptAt?->format('Y-m-d H:i:s.u'),
            ], ['id' => $message->id, 'tenant_id' => $message->tenantId, 'status' => 'processing']);
            if ($affected !== 1) {
                throw new OutboxMessageNotFoundException('The claimed outbox message no longer exists or changed state.');
            }
            $this->insertAttempt(
                $connection,
                $message,
                $nextAttemptAt === null ? 'dead_letter' : 'failed',
                $error,
                $failedAt,
            );
        });
    }

    public function retryDeadLetter(
        string $messageId,
        string $tenantId,
        string $retriedBy,
        DateTimeImmutable $retriedAt
    ): void {
        $this->connection->transactional(function (Connection $connection) use ($messageId, $tenantId, $retriedBy, $retriedAt): void {
            $message = $connection->fetchAssociative(
                'SELECT status FROM wms_integration_outbox WHERE id = :id AND tenant_id = :tenantId FOR UPDATE',
                ['id' => $messageId, 'tenantId' => $tenantId],
            );
            $userExists = $connection->fetchOne(
                'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId',
                ['userId' => $retriedBy, 'tenantId' => $tenantId],
            );
            if ($message === false || $userExists === false) {
                throw new OutboxMessageNotFoundException('An outbox message and retrying user must exist in the tenant.');
            }
            if ($message['status'] !== 'dead_letter') {
                throw new \DomainException('Only dead-letter outbox messages can be retried manually.');
            }
            $connection->update('wms_integration_outbox', [
                'status' => 'pending',
                'attempt_count' => 0,
                'next_attempt_at' => $retriedAt->format('Y-m-d H:i:s.u'),
                'last_error' => null,
                'retried_by' => $retriedBy,
                'retried_at' => $retriedAt->format('Y-m-d H:i:s.u'),
            ], ['id' => $messageId, 'tenant_id' => $tenantId]);
        });
    }

    /** @param array<string, mixed> $row */
    private function hydrateMessage(array $row, int $attemptNumber): OutboxMessage
    {
        $payload = json_decode((string) $row['payload'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($payload)) {
            throw new \LogicException('The outbox payload must decode to an object.');
        }

        return new OutboxMessage(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['event_name'],
            (string) $row['aggregate_type'],
            (string) $row['aggregate_id'],
            $payload,
            (string) $row['created_by'],
            new DateTimeImmutable((string) $row['occurred_at']),
            $attemptNumber,
        );
    }

    private function insertAttempt(
        Connection $connection,
        OutboxMessage $message,
        string $outcome,
        ?string $error,
        DateTimeImmutable $attemptedAt
    ): void {
        $connection->insert('wms_integration_attempt', [
            'id' => Uuid::v7()->toRfc4122(),
            'message_id' => $message->id,
            'attempt_number' => $message->attemptNumber,
            'outcome' => $outcome,
            'error' => $error,
            'attempted_at' => $attemptedAt->format('Y-m-d H:i:s.u'),
        ]);
    }
}
