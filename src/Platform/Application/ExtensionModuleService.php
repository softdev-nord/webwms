<?php

declare(strict_types=1);

namespace WebWMS\Platform\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use DomainException;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\Uid\Uuid;
use Throwable;

readonly class ExtensionModuleService
{
    /** @var array<string, string> */
    public const array RESOURCES = [
        'barcode_profile' => 'extensions.resource.barcode_profile.barcode_configuration',
        'unit_of_measure' => 'extension.resource.unit_of_measure.unit_of_measure',
        'product_extension' => 'extensions.resource.product_extension.item_packaging_configuration',
        'container' => 'extension.resource.container.container_master_data',
        'storage_constraint' => 'extensions.resource.storage_constraint.storage_restriction',
        'optimization_rule' => 'extension.resource.optimization_rule.optimization_rule',
        'login_policy' => 'extensions.resource.login_policy.login_policy',
        'workstation' => 'extension.resource.workstation.workstation',
        'document_template' => 'extensions.resource.document_template.document_report_template',
        'interface_mapping' => 'extension.resource.interface_mapping.interface_mapping',
        'interface_endpoint' => 'extensions.resource.interface_endpoint.interface_endpoint',
        'inbound_scan_profile' => 'extension.resource.inbound_scan_profile.goods_receipt_scan_profile',
        'picking_profile' => 'extensions.resource.picking_profile.picking_profile',
        'packing_profile' => 'extension.resource.packing_profile.packing_sorting_profile',
        'production_profile' => 'extensions.resource.production_profile.production_profile',
        'billing_profile' => 'extension.resource.billing_profile.billing_financial_profile',
        'compliance_profile' => 'extensions.resource.compliance_profile.compliance_sampling_profile',
        'sso_provider' => 'extension.resource.sso_provider.sso_provider',
    ];

    /** @var array<string, string> */
    public const array WORKFLOWS = [
        'container_cycle' => 'extension.workflow.container_cycle.container_cycle',
        'optimization' => 'extension.workflow.optimization.warehouse_optimization',
        'destruction' => 'extension.workflow.destruction.destruction',
        'purchase_proposal' => 'extension.workflow.purchase_proposal.order_proposal',
        'interface_exchange' => 'extension.workflow.interface_exchange.interface_exchange',
        'inbound_scan' => 'extension.workflow.inbound_scan.guided_goods_receipt',
        'assisted_pick' => 'extension.workflow.assisted_pick.assisted_picking',
        'automated_outbound' => 'extension.workflow.automated_outbound.automated_goods_issue',
        'production_order' => 'extension.workflow.production_order.production_order',
        'invoice' => 'extension.workflow.invoice.invoicing_run',
        'compliance_check' => 'extension.workflow.compliance_check.compliance_check',
        'sample_inspection' => 'extension.workflow.sample_inspection.sampling_check',
        'document_output' => 'extension.workflow.document_output.document_output',
    ];

    /** @var array<string, array<string, list<string>>> */
    private const array TRANSITIONS = [
        'container_cycle' => ['available' => ['issued'],
        'issued' => ['returned', 'lost'],
        'returned' => ['available'], 'lost' => []],
        'optimization' => ['proposed' => ['approved', 'rejected'],
        'approved' => ['in_progress'], 'in_progress' => ['completed', 'failed'], 'rejected' => [], 'completed' => [], 'failed' => ['proposed']],
        'destruction' => ['requested' => ['approved', 'rejected'], 'approved' => ['destroyed'], 'rejected' => [], 'destroyed' => []],
        'purchase_proposal' => ['proposed' => ['approved', 'rejected'], 'approved' => ['ordered'], 'rejected' => [], 'ordered' => []],
        'interface_exchange' => ['received' => ['validated', 'failed'], 'validated' => ['processed', 'failed'], 'processed' => ['acknowledged'], 'failed' => ['received'], 'acknowledged' => []],
        'inbound_scan' => ['captured' => ['validated', 'correction'], 'correction' => ['captured'], 'validated' => ['booked'], 'booked' => []],
        'assisted_pick' => ['planned' => ['released', 'cancelled'], 'released' => ['in_progress'], 'in_progress' => ['completed', 'exception'], 'exception' => ['in_progress', 'cancelled'], 'completed' => [], 'cancelled' => []],
        'automated_outbound' => ['planned' => ['sorting', 'cancelled'], 'sorting' => ['packing'], 'packing' => ['labelled', 'exception'], 'exception' => ['packing', 'cancelled'], 'labelled' => ['shipped'], 'shipped' => [], 'cancelled' => []],
        'production_order' => ['planned' => ['released', 'cancelled'], 'released' => ['supplied'], 'supplied' => ['in_progress'], 'in_progress' => ['paused', 'completed'], 'paused' => ['in_progress'], 'completed' => ['received'], 'received' => [], 'cancelled' => []],
        'invoice' => ['draft' => ['approved', 'cancelled'], 'approved' => ['issued'], 'issued' => ['exported', 'cancelled'], 'exported' => ['paid'], 'paid' => [], 'cancelled' => []],
        'compliance_check' => ['pending' => ['cleared', 'hit'], 'hit' => ['released', 'blocked'], 'cleared' => [], 'released' => [], 'blocked' => []],
        'sample_inspection' => ['planned' => ['sampled'], 'sampled' => ['accepted', 'rejected'], 'accepted' => [], 'rejected' => []],
        'document_output' => ['queued' => ['rendered', 'failed'], 'rendered' => ['printed', 'archived'], 'failed' => ['queued'], 'printed' => ['archived'], 'archived' => []],
    ];

    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * @return array{
     *     resources: array<string, string>,
     *     workflows: list<string>,
     *     configurations: list<array<string, mixed>>,
     *     workItems: list<array<string, mixed>>,
     *     loginEvents: list<array<string, mixed>>
     *         }
     * @throws DBALException
     */
    public function workspace(string $tenantId): array
    {
        return [
            'resources' => self::RESOURCES,
            'workflows' => self::WORKFLOWS,
            'configurations' => $this->connection->fetchAllAssociative('SELECT * FROM wms_extension_configuration WHERE tenant_id = :tenantId ORDER BY resource_type, code', ['tenantId' => $tenantId]),
            'workItems' => $this->connection->fetchAllAssociative('SELECT * FROM wms_extension_work_item WHERE tenant_id = :tenantId ORDER BY changed_at DESC LIMIT 250', ['tenantId' => $tenantId]),
            'loginEvents' => $this->connection->fetchAllAssociative('SELECT * FROM wms_login_event WHERE tenant_id = :tenantId ORDER BY occurred_at DESC LIMIT 100', ['tenantId' => $tenantId]),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     * @throws DBALException
     */
    public function configurations(string $tenantId, string $resource): array
    {
        $this->assertResource($resource);

        return $this->connection->fetchAllAssociative(
            'SELECT * FROM wms_extension_configuration WHERE tenant_id = :tenantId AND resource_type = :resource ORDER BY code',
            ['tenantId' => $tenantId, 'resource' => $resource],
        );
    }

    /**
     * @return list<array<string, mixed>>
     * @throws DBALException
     */
    public function workItems(string $tenantId, string $workflow): array
    {
        $this->assertWorkflow($workflow);

        return $this->connection->fetchAllAssociative(
            'SELECT * FROM wms_extension_work_item WHERE tenant_id = :tenantId AND workflow_type = :workflow ORDER BY changed_at DESC LIMIT 250',
            ['tenantId' => $tenantId, 'workflow' => $workflow],
        );
    }

    /**
     * @return array<string, mixed>
     * @throws DBALException
     */
    public function configuration(string $tenantId, string $resource, string $id): array
    {
        $this->assertResource($resource);
        $row = $this->connection->fetchAssociative('SELECT * FROM wms_extension_configuration WHERE id = :id AND tenant_id = :tenantId AND resource_type = :resource', ['id' => $id, 'tenantId' => $tenantId, 'resource' => $resource]);
        if ($row === false) {
            throw new DomainException('Die Konfiguration wurde nicht gefunden.');
        }

        return $row;
    }

    /**
     * @param array<string, mixed> $configuration
     * @throws Throwable
     */
    public function saveConfiguration(string $tenantId, string $actorId, string $resource, ?string $id, string $code, string $name, array $configuration, bool $active, DateTimeImmutable $now): string
    {
        $this->assertResource($resource);
        $code = $this->required($code, 80);
        $name = $this->required($name, 160);
        $payload = json_encode($configuration, JSON_THROW_ON_ERROR);
        $id ??= Uuid::v7()->toRfc4122();
        $row = ['resource_type' => $resource, 'code' => $code, 'name' => $name, 'configuration_json' => $payload, 'active' => $active ? 1 : 0, 'updated_by' => $actorId, 'updated_at' => $this->date($now)];
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $id, $row, $now): void {
            $exists = $connection->fetchOne('SELECT 1 FROM wms_extension_configuration WHERE id = :id AND tenant_id = :tenantId', ['id' => $id, 'tenantId' => $tenantId]) !== false;
            if ($exists) {
                $connection->update('wms_extension_configuration', $row, ['id' => $id, 'tenant_id' => $tenantId]);
            } else {
                $connection->insert('wms_extension_configuration', ['id' => $id, 'tenant_id' => $tenantId, ...$row, 'created_by' => $actorId, 'created_at' => $this->date($now)]);
            }

            $this->audit($connection, $tenantId, $actorId, 'extension_configuration', $id, $exists ? 'updated' : 'created', $row, $now);
        });

        return $id;
    }

    /**
     * @param array<string, mixed> $payload
     * @throws Throwable
     */
    public function createWorkItem(string $tenantId, string $actorId, string $workflow, string $reference, array $payload, DateTimeImmutable $now): string
    {
        $this->assertWorkflow($workflow);
        $states = self::TRANSITIONS[$workflow];
        $id = Uuid::v7()->toRfc4122();
        $status = (string) array_key_first($states);
        $row = ['id' => $id, 'tenant_id' => $tenantId, 'workflow_type' => $workflow, 'reference' => $this->required($reference, 120), 'status' => $status, 'payload_json' => json_encode($payload, JSON_THROW_ON_ERROR), 'created_by' => $actorId, 'created_at' => $this->date($now), 'changed_by' => $actorId, 'changed_at' => $this->date($now)];
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $id, $row, $now): void {
            $connection->insert('wms_extension_work_item', $row);
            $this->audit($connection, $tenantId, $actorId, 'extension_work_item', $id, 'created', $row, $now);
        });

        return $id;
    }

    /**
     * @return array<string, mixed>
     * @throws DBALException
     */
    public function workItem(string $tenantId, string $id): array
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM wms_extension_work_item WHERE id = :id AND tenant_id = :tenantId', ['id' => $id, 'tenantId' => $tenantId]);
        if ($row === false) {
            throw new DomainException('Der Vorgang wurde nicht gefunden.');
        }

        return $row;
    }

    /** @return list<string> */
    public function allowedTransitions(string $workflow, string $status): array
    {
        return self::TRANSITIONS[$workflow][$status] ?? [];
    }

    /**
     * @throws Throwable
     */
    public function transition(string $tenantId, string $actorId, string $id, string $target, DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $id, $target, $now): void {
            $item = $connection->fetchAssociative('SELECT workflow_type, status FROM wms_extension_work_item WHERE id = :id AND tenant_id = :tenantId FOR UPDATE', ['id' => $id, 'tenantId' => $tenantId]);
            if ($item === false || !is_string($item['workflow_type']) || !is_string($item['status'])) {
                throw new DomainException('Der Vorgang wurde nicht gefunden.');
            }

            if (!in_array($target, $this->allowedTransitions($item['workflow_type'], $item['status']), true)) {
                throw new DomainException('Dieser Statuswechsel ist nicht zulässig.');
            }

            $connection->update('wms_extension_work_item', ['status' => $target, 'changed_by' => $actorId, 'changed_at' => $this->date($now)], ['id' => $id, 'tenant_id' => $tenantId]);
            $this->audit($connection, $tenantId, $actorId, 'extension_work_item', $id, 'status_changed', ['from' => $item['status'], 'to' => $target], $now);
        });
    }

    /**
     * @throws DBALException
     */
    public function recordLogin(?string $tenantId, string $identifier, bool $successful, ?string $ip, ?string $userAgent, ?string $failureReason, DateTimeImmutable $now): void
    {
        if ($tenantId !== null && $this->connection->fetchOne('SELECT 1 FROM wms_tenant WHERE id = :id', ['id' => $tenantId]) === false) {
            $tenantId = null;
        }

        $this->connection->insert('wms_login_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'user_identifier' => mb_substr($identifier, 0, 190), 'successful' => $successful ? 1 : 0, 'client_ip' => $ip, 'user_agent' => $userAgent === null ? null : mb_substr($userAgent, 0, 255), 'failure_reason' => $failureReason === null ? null : mb_substr($failureReason, 0, 255), 'occurred_at' => $this->date($now)]);
    }

    private function assertResource(string $resource): void
    {
        if (!isset(self::RESOURCES[$resource])) {
            throw new InvalidArgumentException('Die Konfigurationsressource ist unbekannt.');
        }
    }

    private function assertWorkflow(string $workflow): void
    {
        if (!isset(self::TRANSITIONS[$workflow])) {
            throw new InvalidArgumentException('Der Workflow ist unbekannt.');
        }
    }

    private function required(string $value, int $maxLength): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException('Ein Pflichtwert fehlt oder ist zu lang.');
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $payload
     * @throws DBALException|JsonException
     */
    private function audit(Connection $connection, string $tenantId, string $actorId, string $aggregateType, string $aggregateId, string $eventType, array $payload, DateTimeImmutable $now): void
    {
        $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => $aggregateType, 'aggregate_id' => $aggregateId, 'event_type' => $eventType, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $this->date($now)]);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
