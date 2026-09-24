<?php

declare(strict_types=1);

namespace WebWMS\Platform\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class ExtensionModuleService
{
    /** @var array<string, string> */
    public const RESOURCES = [
        'barcode_profile' => 'Barcodekonfiguration', 'unit_of_measure' => 'Mengeneinheit',
        'product_extension' => 'Artikel-/Gebindekonfiguration', 'container' => 'Behälterstamm',
        'storage_constraint' => 'Lagerrestriktion', 'optimization_rule' => 'Optimierungsregel',
        'login_policy' => 'Login-Richtlinie', 'workstation' => 'Arbeitsstation',
        'document_template' => 'Dokument-/Reportvorlage', 'interface_mapping' => 'Schnittstellen-Mapping',
        'interface_endpoint' => 'Schnittstellen-Endpunkt', 'inbound_scan_profile' => 'Wareneingang-Scanprofil',
        'picking_profile' => 'Pickprofil', 'packing_profile' => 'Pack-/Sortierprofil',
        'production_profile' => 'Produktionsprofil', 'billing_profile' => 'Billing-/Finanzprofil',
        'compliance_profile' => 'Compliance-/Stichprobenprofil', 'sso_provider' => 'SSO-Provider',
    ];

    /** @var array<string, string> */
    public const WORKFLOWS = [
        'container_cycle' => 'Behälterkreislauf', 'optimization' => 'Lageroptimierung',
        'destruction' => 'Vernichtung', 'purchase_proposal' => 'Bestellvorschlag',
        'interface_exchange' => 'Schnittstellenaustausch', 'inbound_scan' => 'Geführter Wareneingang',
        'assisted_pick' => 'Assistierte Kommissionierung', 'automated_outbound' => 'Automatisierter Warenausgang',
        'production_order' => 'Produktionsauftrag', 'invoice' => 'Rechnungslauf',
        'compliance_check' => 'Compliance-Prüfung', 'sample_inspection' => 'Stichprobenprüfung',
        'document_output' => 'Dokumentausgabe',
    ];

    /** @var array<string, array<string, list<string>>> */
    private const TRANSITIONS = [
        'container_cycle' => ['available' => ['issued'], 'issued' => ['returned', 'lost'], 'returned' => ['available'], 'lost' => []],
        'optimization' => ['proposed' => ['approved', 'rejected'], 'approved' => ['in_progress'], 'in_progress' => ['completed', 'failed'], 'rejected' => [], 'completed' => [], 'failed' => ['proposed']],
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

    public function __construct(private Connection $connection)
    {
    }

    /** @return array{resources: array<string, string>, workflows: list<string>, configurations: list<array<string, mixed>>, workItems: list<array<string, mixed>>, loginEvents: list<array<string, mixed>>} */
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

    /** @return list<array<string, mixed>> */
    public function configurations(string $tenantId, string $resource): array
    {
        $this->assertResource($resource);

        return $this->connection->fetchAllAssociative(
            'SELECT * FROM wms_extension_configuration WHERE tenant_id = :tenantId AND resource_type = :resource ORDER BY code',
            ['tenantId' => $tenantId, 'resource' => $resource],
        );
    }

    /** @return list<array<string, mixed>> */
    public function workItems(string $tenantId, string $workflow): array
    {
        $this->assertWorkflow($workflow);

        return $this->connection->fetchAllAssociative(
            'SELECT * FROM wms_extension_work_item WHERE tenant_id = :tenantId AND workflow_type = :workflow ORDER BY changed_at DESC LIMIT 250',
            ['tenantId' => $tenantId, 'workflow' => $workflow],
        );
    }

    /** @return array<string, mixed> */
    public function configuration(string $tenantId, string $resource, string $id): array
    {
        $this->assertResource($resource);
        $row = $this->connection->fetchAssociative('SELECT * FROM wms_extension_configuration WHERE id = :id AND tenant_id = :tenantId AND resource_type = :resource', ['id' => $id, 'tenantId' => $tenantId, 'resource' => $resource]);
        if ($row === false) {
            throw new \DomainException('Die Konfiguration wurde nicht gefunden.');
        }

        return $row;
    }

    /** @param array<string, mixed> $configuration */
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

    /** @param array<string, mixed> $payload */
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

    /** @return array<string, mixed> */
    public function workItem(string $tenantId, string $id): array
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM wms_extension_work_item WHERE id = :id AND tenant_id = :tenantId', ['id' => $id, 'tenantId' => $tenantId]);
        if ($row === false) {
            throw new \DomainException('Der Vorgang wurde nicht gefunden.');
        }

        return $row;
    }

    /** @return list<string> */
    public function allowedTransitions(string $workflow, string $status): array
    {
        return self::TRANSITIONS[$workflow][$status] ?? [];
    }

    public function transition(string $tenantId, string $actorId, string $id, string $target, DateTimeImmutable $now): void
    {
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $id, $target, $now): void {
            $item = $connection->fetchAssociative('SELECT workflow_type, status FROM wms_extension_work_item WHERE id = :id AND tenant_id = :tenantId FOR UPDATE', ['id' => $id, 'tenantId' => $tenantId]);
            if ($item === false || !is_string($item['workflow_type']) || !is_string($item['status'])) {
                throw new \DomainException('Der Vorgang wurde nicht gefunden.');
            }
            if (!in_array($target, $this->allowedTransitions($item['workflow_type'], $item['status']), true)) {
                throw new \DomainException('Dieser Statuswechsel ist nicht zulässig.');
            }
            $connection->update('wms_extension_work_item', ['status' => $target, 'changed_by' => $actorId, 'changed_at' => $this->date($now)], ['id' => $id, 'tenant_id' => $tenantId]);
            $this->audit($connection, $tenantId, $actorId, 'extension_work_item', $id, 'status_changed', ['from' => $item['status'], 'to' => $target], $now);
        });
    }

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
            throw new \InvalidArgumentException('Die Konfigurationsressource ist unbekannt.');
        }
    }

    private function assertWorkflow(string $workflow): void
    {
        if (!isset(self::TRANSITIONS[$workflow])) {
            throw new \InvalidArgumentException('Der Workflow ist unbekannt.');
        }
    }

    private function required(string $value, int $maxLength): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > $maxLength) {
            throw new \InvalidArgumentException('Ein Pflichtwert fehlt oder ist zu lang.');
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function audit(Connection $connection, string $tenantId, string $actorId, string $aggregateType, string $aggregateId, string $eventType, array $payload, DateTimeImmutable $now): void
    {
        $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => $aggregateType, 'aggregate_id' => $aggregateId, 'event_type' => $eventType, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $this->date($now)]);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
