<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use DomainException;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\IntegrationStatusEvent;
use WebWMS\Integration\Domain\OutboxRepository;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;

final readonly class OutboundProcessService
{
    public function __construct(
        private Connection $connection,
        private OutboxRepository $outbox
    ) {
    }

    public function cancelOrder(string $tenantId, string $orderId, string $reason, string $actorId, DateTimeImmutable $now): void
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('A cancellation reason is required.');
        }
        $this->assertActor($tenantId, $actorId);
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $orderId, $reason, $actorId, $now): void {
            $status = $connection->fetchOne('SELECT status FROM wms_outbound_order WHERE id = :id AND tenant_id = :tenantId FOR UPDATE', ['id' => $orderId, 'tenantId' => $tenantId]);
            if ($status !== 'imported') {
                throw new DomainException('Only an imported outbound order may be cancelled.');
            }
            $connection->update('wms_outbound_order', ['status' => 'cancelled', 'cancelled_by' => $actorId, 'cancelled_at' => $this->date($now), 'cancellation_reason' => trim($reason)], ['id' => $orderId, 'tenant_id' => $tenantId]);
        });
    }

    public function inspectPickList(string $tenantId, string $pickListId, bool $complete, bool $condition, bool $customerCheck, string $note, string $actorId, DateTimeImmutable $now): string
    {
        $this->assertActor($tenantId, $actorId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($id, $tenantId, $pickListId, $complete, $condition, $customerCheck, $note, $actorId, $now): void {
            $status = $connection->fetchOne('SELECT status FROM wms_pick_list WHERE id = :id AND tenant_id = :tenantId FOR UPDATE', ['id' => $pickListId, 'tenantId' => $tenantId]);
            if ($status !== 'completed') {
                throw new DomainException('Only a completed pick list may pass outbound quality control.');
            }
            $decision = $complete && $condition && $customerCheck ? 'released' : 'blocked';
            if ($decision === 'blocked' && trim($note) === '') {
                throw new InvalidArgumentException('A blocked quality check requires a note.');
            }
            $connection->insert('wms_outbound_quality_check', ['id' => $id, 'tenant_id' => $tenantId, 'pick_list_id' => $pickListId, 'completeness_passed' => $complete, 'condition_passed' => $condition, 'customer_check_passed' => $customerCheck, 'note' => trim($note), 'decision' => $decision, 'checked_by' => $actorId, 'checked_at' => $this->date($now)]);
        });

        return $id;
    }

    public function createShippingRule(string $tenantId, string $code, string $name, string $carrier, string $service, int $minWeight, int $maxWeight, int $priority, string $actorId, DateTimeImmutable $now): string
    {
        if (trim($code) === '' || trim($name) === '' || trim($carrier) === '' || trim($service) === '' || $minWeight < 0 || $maxWeight < 1 || $minWeight > $maxWeight) {
            throw new InvalidArgumentException('The shipping rule weight range is invalid.');
        }
        $this->assertActor($tenantId, $actorId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_shipping_rule', ['id' => $id, 'tenant_id' => $tenantId, 'code' => mb_strtoupper(trim($code)), 'name' => trim($name), 'carrier' => mb_strtoupper(trim($carrier)), 'service' => trim($service), 'min_weight_grams' => $minWeight, 'max_weight_grams' => $maxWeight, 'priority' => $priority, 'active' => 1, 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    /** @return array<string, mixed> */
    public function selectShippingRule(string $tenantId, int $weight): array
    {
        if ($weight < 1) {
            throw new InvalidArgumentException('The shipment weight must be positive.');
        }
        $rule = $this->connection->fetchAssociative('SELECT id, code, carrier, service FROM wms_shipping_rule WHERE tenant_id = :tenantId AND active = 1 AND :weight BETWEEN min_weight_grams AND max_weight_grams ORDER BY priority, code LIMIT 1', ['tenantId' => $tenantId, 'weight' => $weight]);
        if ($rule === false) {
            throw new InventoryReferenceNotFoundException('No active shipping rule matches the shipment weight.');
        }

        return $rule;
    }

    public function recordTracking(string $tenantId, string $shipmentId, string $status, ?string $location, string $description, string $source, DateTimeImmutable $occurredAt, string $actorId, DateTimeImmutable $now): string
    {
        if (trim($status) === '' || trim($description) === '' || trim($source) === '') {
            throw new InvalidArgumentException('Tracking status, description and source are required.');
        }
        $this->assertActor($tenantId, $actorId);
        $id = Uuid::v7()->toRfc4122();
        $normalizedStatus = mb_strtolower(trim($status));
        $this->connection->transactional(function (Connection $connection) use ($id, $tenantId, $shipmentId, $normalizedStatus, $location, $description, $source, $occurredAt, $actorId, $now): void {
            $exists = $connection->fetchOne('SELECT 1 FROM wms_shipment WHERE id = :id AND tenant_id = :tenantId', ['id' => $shipmentId, 'tenantId' => $tenantId]);
            if ($exists === false) {
                throw new InventoryReferenceNotFoundException('The shipment must exist in the tenant.');
            }
            $connection->insert('wms_tracking_event', ['id' => $id, 'tenant_id' => $tenantId, 'shipment_id' => $shipmentId, 'status' => $normalizedStatus, 'location' => $location, 'description' => trim($description), 'source' => mb_strtolower(trim($source)), 'occurred_at' => $this->date($occurredAt), 'recorded_by' => $actorId, 'recorded_at' => $this->date($now)]);
            $this->outbox->append(new IntegrationStatusEvent(Uuid::v7()->toRfc4122(), $tenantId, 'shipment.tracking_updated', 'shipment', $shipmentId, ['trackingEventId' => $id, 'status' => $normalizedStatus, 'location' => $location, 'description' => trim($description)], $actorId, $occurredAt));
        });

        return $id;
    }

    public function generateDocument(string $tenantId, string $aggregateType, string $aggregateId, string $type, string $number, string $content, string $actorId, DateTimeImmutable $now): string
    {
        if (!in_array($type, ['delivery_note', 'packing_list', 'loading_list', 'cmr'], true) || trim($number) === '' || trim($content) === '') {
            throw new InvalidArgumentException('Document type and content are required.');
        }
        $this->assertActor($tenantId, $actorId);
        $this->assertAggregate($tenantId, $aggregateType, $aggregateId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_shipping_document', ['id' => $id, 'tenant_id' => $tenantId, 'aggregate_type' => $aggregateType, 'aggregate_id' => $aggregateId, 'document_type' => $type, 'document_number' => trim($number), 'content' => $content, 'content_type' => 'text/html; charset=UTF-8', 'checksum' => hash('sha256', $content), 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    /** @param list<array{destinationName: string, destinationAddress: string, shipmentId: string|null}> $stops */
    public function createTour(string $tenantId, string $code, string $carrier, string $vehicle, int $maxWeight, DateTimeImmutable $departureAt, array $stops, string $actorId, DateTimeImmutable $now): string
    {
        if (trim($code) === '' || trim($carrier) === '' || trim($vehicle) === '' || $maxWeight < 1 || $stops === []) {
            throw new InvalidArgumentException('A tour requires a positive weight limit and at least one stop.');
        }
        foreach ($stops as $stop) {
            if (trim($stop['destinationName']) === '' || trim($stop['destinationAddress']) === '') {
                throw new InvalidArgumentException('Every tour stop requires a destination name and address.');
            }
        }
        $this->assertActor($tenantId, $actorId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($id, $tenantId, $code, $carrier, $vehicle, $maxWeight, $departureAt, $stops, $actorId, $now): void {
            $connection->insert('wms_transport_tour', ['id' => $id, 'tenant_id' => $tenantId, 'code' => mb_strtoupper(trim($code)), 'carrier' => mb_strtoupper(trim($carrier)), 'vehicle_reference' => trim($vehicle), 'max_weight_grams' => $maxWeight, 'departure_at' => $this->date($departureAt), 'status' => 'planned', 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            foreach ($stops as $index => $stop) {
                if ($stop['shipmentId'] !== null && $connection->fetchOne('SELECT 1 FROM wms_shipment WHERE id = :id AND tenant_id = :tenantId', ['id' => $stop['shipmentId'], 'tenantId' => $tenantId]) === false) {
                    throw new InventoryReferenceNotFoundException('Each assigned shipment must exist in the tenant.');
                }
                $connection->insert('wms_tour_stop', ['id' => Uuid::v7()->toRfc4122(), 'tour_id' => $id, 'sequence_number' => $index + 1, 'destination_name' => trim($stop['destinationName']), 'destination_address' => trim($stop['destinationAddress']), 'shipment_id' => $stop['shipmentId'], 'status' => 'planned']);
            }
        });

        return $id;
    }

    public function createWeightConstraint(string $tenantId, string $scope, ?string $reference, int $maxWeight, string $actorId, DateTimeImmutable $now): string
    {
        if (!in_array($scope, ['package', 'carrier', 'vehicle', 'tour'], true) || $maxWeight < 1) {
            throw new InvalidArgumentException('Weight constraint scope or limit is invalid.');
        }
        $this->assertActor($tenantId, $actorId);
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_weight_constraint', ['id' => $id, 'tenant_id' => $tenantId, 'scope' => $scope, 'reference_code' => $reference, 'max_weight_grams' => $maxWeight, 'active' => 1, 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    public function assertPackageWeight(string $tenantId, int $weight): void
    {
        $limit = $this->connection->fetchOne("SELECT MIN(max_weight_grams) FROM wms_weight_constraint WHERE tenant_id = :tenantId AND scope = 'package' AND active = 1", ['tenantId' => $tenantId]);
        if ($limit !== false && $limit !== null && $weight > (int) $limit) {
            throw new DomainException(sprintf('Package weight exceeds the configured limit of %d grams.', (int) $limit));
        }
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if ($this->connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId', ['id' => $actorId, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function assertAggregate(string $tenantId, string $type, string $id): void
    {
        $table = match ($type) {
            'shipment' => 'wms_shipment',
            'loading_manifest' => 'wms_loading_manifest',
            'tour' => 'wms_transport_tour',
            default => throw new InvalidArgumentException('The shipping document aggregate type is not supported.'),
        };
        if ($this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id AND tenant_id = :tenantId', $table), ['id' => $id, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('The shipping document aggregate must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
