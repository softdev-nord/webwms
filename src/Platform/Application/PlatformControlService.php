<?php

declare(strict_types=1);

namespace WebWMS\Platform\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class PlatformControlService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /** @return array<string, mixed> */
    public function workspace(string $tenantId): array
    {
        return [
            'tasks' => $this->all('wms_shopfloor_task', $tenantId, 'priority DESC, planned_for, created_at'),
            'kpis' => $this->kpis($tenantId),
            'dashboards' => $this->all('wms_dashboard', $tenantId, 'code'),
            'partnerAccounts' => $this->connection->fetchAllAssociative('SELECT a.*, p.code partner_code, p.name partner_name, u.email FROM wms_partner_account a INNER JOIN wms_business_partner p ON p.id = a.business_partner_id INNER JOIN wms_user_account u ON u.id = a.user_id WHERE a.tenant_id = :tenantId ORDER BY p.code, u.email', ['tenantId' => $tenantId]),
            'partners' => $this->all('wms_business_partner', $tenantId, 'code'),
            'users' => $this->connection->fetchAllAssociative('SELECT id, email, display_name, status FROM wms_user_account WHERE tenant_id = :tenantId ORDER BY email', ['tenantId' => $tenantId]),
            'rules' => $this->all('wms_automation_rule', $tenantId, 'event_name, code'),
            'executions' => $this->connection->fetchAllAssociative('SELECT e.*, r.code rule_code FROM wms_automation_execution e INNER JOIN wms_automation_rule r ON r.id = e.rule_id WHERE e.tenant_id = :tenantId ORDER BY e.executed_at DESC LIMIT 100', ['tenantId' => $tenantId]),
            'storageFeeRules' => $this->connection->fetchAllAssociative('SELECT r.*, p.code partner_code FROM wms_storage_fee_rule r INNER JOIN wms_business_partner p ON p.id = r.business_partner_id WHERE r.tenant_id = :tenantId ORDER BY r.code', ['tenantId' => $tenantId]),
            'services' => $this->all('wms_value_added_service', $tenantId, 'code'),
            'billableLines' => $this->connection->fetchAllAssociative('SELECT l.*, p.code partner_code FROM wms_billable_line l INNER JOIN wms_business_partner p ON p.id = l.business_partner_id WHERE l.tenant_id = :tenantId ORDER BY l.service_date DESC, l.created_at DESC LIMIT 200', ['tenantId' => $tenantId]),
            'media' => $this->connection->fetchAllAssociative('SELECT id, aggregate_type, aggregate_id, filename, mime_type, byte_size, checksum, captured_at FROM wms_media_asset WHERE tenant_id = :tenantId ORDER BY captured_at DESC LIMIT 100', ['tenantId' => $tenantId]),
            'printRoutes' => $this->connection->fetchAllAssociative('SELECT r.*, p.name printer_name, s.code site_code FROM wms_print_routing_rule r INNER JOIN wms_printer p ON p.id = r.printer_id LEFT JOIN wms_site s ON s.id = r.site_id WHERE r.tenant_id = :tenantId ORDER BY r.priority, r.code', ['tenantId' => $tenantId]),
            'printers' => $this->connection->fetchAllAssociative('SELECT id, name, active FROM wms_printer WHERE tenant_id = :tenantId ORDER BY name', ['tenantId' => $tenantId]),
            'sites' => $this->all('wms_site', $tenantId, 'code'),
        ];
    }

    /** @return array<string, mixed> */
    public function partnerPortal(string $tenantId, string $userId): array
    {
        $account = $this->connection->fetchAssociative('SELECT a.*, p.code partner_code, p.name partner_name FROM wms_partner_account a INNER JOIN wms_business_partner p ON p.id = a.business_partner_id WHERE a.tenant_id = :tenantId AND a.user_id = :userId AND a.active = 1 AND p.active = 1', ['tenantId' => $tenantId, 'userId' => $userId]);
        if ($account === false) {
            throw new \DomainException('Für diesen Benutzer ist kein aktiver Partnerzugang vorhanden.');
        }
        $permissions = json_decode((string) $account['permissions'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($permissions)) {
            $permissions = [];
        }

        return [
            'account' => $account,
            'billableLines' => in_array('billing.read', $permissions, true) ? $this->connection->fetchAllAssociative('SELECT source_type, source_reference, description, quantity, amount, currency, service_date, status FROM wms_billable_line WHERE tenant_id = :tenantId AND business_partner_id = :partnerId ORDER BY service_date DESC LIMIT 100', ['tenantId' => $tenantId, 'partnerId' => $account['business_partner_id']]) : [],
            'media' => in_array('media.read', $permissions, true) ? $this->connection->fetchAllAssociative("SELECT id, aggregate_type, aggregate_id, filename, mime_type, captured_at FROM wms_media_asset WHERE tenant_id = :tenantId AND aggregate_type = 'business_partner' AND aggregate_id = :partnerId ORDER BY captured_at DESC", ['tenantId' => $tenantId, 'partnerId' => $account['business_partner_id']]) : [],
        ];
    }

    /** @param array<string, mixed> $values */
    public function create(string $tenantId, string $actorId, string $resource, array $values, DateTimeImmutable $now): string
    {
        $definitions = self::definitions();
        if (!isset($definitions[$resource])) {
            throw new \InvalidArgumentException('Die Plattformressource ist unbekannt.');
        }
        $definition = $definitions[$resource];
        $id = Uuid::v7()->toRfc4122();
        $row = ['id' => $id, 'tenant_id' => $tenantId];
        foreach ($definition['fields'] as $field => $type) {
            $row[$field] = $this->value($field, $type, $values[$field] ?? null);
        }
        foreach ($definition['references'] as $field => $table) {
            if ($row[$field] !== null) {
                $this->assertReference($tenantId, $table, (string) $row[$field], $field);
            }
        }
        $row['created_by'] = $actorId;
        $row['created_at'] = $this->date($now);
        if ($resource === 'task') {
            $row['status'] = 'planned';
        }
        $this->connection->transactional(function (Connection $connection) use ($definition, $row, $tenantId, $actorId, $resource, $id, $now): void {
            $connection->insert($definition['table'], $row);
            $this->audit($connection, $tenantId, $actorId, $resource, $id, 'created', $row, $now);
            $this->index($connection, $tenantId, $resource, $id, (string) ($row['name'] ?? $row['title'] ?? $row['code'] ?? $id), json_encode($row, JSON_THROW_ON_ERROR), $now);
        });

        return $id;
    }

    public function transitionTask(string $tenantId, string $actorId, string $taskId, string $status, DateTimeImmutable $now): void
    {
        if (!in_array($status, ['planned', 'released', 'in_progress', 'completed', 'cancelled'], true)) {
            throw new \InvalidArgumentException('Der Shopfloor-Status ist ungültig.');
        }
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $taskId, $status, $now): void {
            $current = $connection->fetchOne('SELECT status FROM wms_shopfloor_task WHERE id = :id AND tenant_id = :tenantId FOR UPDATE', ['id' => $taskId, 'tenantId' => $tenantId]);
            $allowed = ['planned' => ['released', 'cancelled'], 'released' => ['in_progress', 'cancelled'], 'in_progress' => ['completed', 'cancelled'], 'completed' => [], 'cancelled' => []];
            if (!is_string($current) || !in_array($status, $allowed[$current] ?? [], true)) {
                throw new \DomainException('Dieser Shopfloor-Statuswechsel ist nicht zulässig.');
            }
            $connection->update('wms_shopfloor_task', ['status' => $status, 'changed_by' => $actorId, 'changed_at' => $this->date($now)], ['id' => $taskId, 'tenant_id' => $tenantId]);
            $this->audit($connection, $tenantId, $actorId, 'task', $taskId, 'status_changed', ['from' => $current, 'to' => $status], $now);
        });
    }

    /** @param array<string, mixed> $payload */
    public function executeEvent(string $tenantId, string $actorId, string $eventName, array $payload, DateTimeImmutable $now): int
    {
        $eventName = $this->required('event_name', $eventName, 100);
        $rules = $this->connection->fetchAllAssociative('SELECT * FROM wms_automation_rule WHERE tenant_id = :tenantId AND event_name = :eventName AND active = 1 ORDER BY code', ['tenantId' => $tenantId, 'eventName' => $eventName]);
        $executed = 0;
        foreach ($rules as $rule) {
            $conditions = json_decode((string) $rule['conditions_json'], true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($conditions)) {
                continue;
            }
            $matches = true;
            foreach ($conditions as $key => $expected) {
                if (!is_string($key) || ($payload[$key] ?? null) !== $expected) {
                    $matches = false;

                    break;
                }
            }
            if (!$matches) {
                continue;
            }
            $this->connection->insert('wms_automation_execution', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'rule_id' => $rule['id'], 'event_name' => $eventName, 'event_payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'status' => 'queued', 'result_message' => sprintf('Aktion %s wurde zur Ausführung vorgemerkt.', $rule['action_type']), 'executed_by' => $actorId, 'executed_at' => $this->date($now)]);
            ++$executed;
        }

        return $executed;
    }

    public function billStorage(string $tenantId, string $actorId, string $ruleId, float $quantity, int $days, string $reference, DateTimeImmutable $now): string
    {
        if ($quantity <= 0 || $days < 1 || trim($reference) === '') {
            throw new \InvalidArgumentException('Menge, Lagertage und Referenz sind erforderlich.');
        }
        $rule = $this->connection->fetchAssociative('SELECT * FROM wms_storage_fee_rule WHERE id = :id AND tenant_id = :tenantId AND active = 1', ['id' => $ruleId, 'tenantId' => $tenantId]);
        if ($rule === false) {
            throw new \InvalidArgumentException('Die aktive Lagergeldregel wurde nicht gefunden.');
        }
        $billableDays = max(0, $days - (int) $rule['free_days']);

        return $this->bill($tenantId, $actorId, (string) $rule['business_partner_id'], 'storage', $reference, (string) $rule['name'], $quantity * $billableDays, (float) $rule['price_per_unit_day'], (string) $rule['currency'], $now);
    }

    public function billService(string $tenantId, string $actorId, string $serviceId, string $partnerId, float $quantity, string $reference, DateTimeImmutable $now): string
    {
        if ($quantity <= 0 || trim($reference) === '') {
            throw new \InvalidArgumentException('Leistungsmenge und Referenz sind erforderlich.');
        }
        $this->assertReference($tenantId, 'wms_business_partner', $partnerId, 'business_partner_id');
        $service = $this->connection->fetchAssociative('SELECT * FROM wms_value_added_service WHERE id = :id AND tenant_id = :tenantId AND active = 1', ['id' => $serviceId, 'tenantId' => $tenantId]);
        if ($service === false) {
            throw new \InvalidArgumentException('Die aktive Zusatzleistung wurde nicht gefunden.');
        }

        return $this->bill($tenantId, $actorId, $partnerId, 'vas', $reference, (string) $service['name'], $quantity, (float) $service['unit_price'], (string) $service['currency'], $now);
    }

    public function captureMedia(string $tenantId, string $actorId, string $aggregateType, string $aggregateId, string $filename, string $mimeType, string $content, DateTimeImmutable $now): string
    {
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], true) || $content === '' || strlen($content) > 5_000_000) {
            throw new \InvalidArgumentException('Erlaubt sind JPG, PNG, WebP und PDF bis 5 MB.');
        }
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_media_asset', ['id' => $id, 'tenant_id' => $tenantId, 'aggregate_type' => $this->required('aggregate_type', $aggregateType, 40), 'aggregate_id' => $this->required('aggregate_id', $aggregateId, 36), 'filename' => $this->required('filename', $filename, 255), 'mime_type' => $mimeType, 'byte_size' => strlen($content), 'checksum' => hash('sha256', $content), 'content' => $content, 'captured_by' => $actorId, 'captured_at' => $this->date($now)]);

        return $id;
    }

    /** @return list<array<string, mixed>> */
    public function search(string $tenantId, string $term): array
    {
        $term = trim($term);
        if (mb_strlen($term) < 2) {
            return [];
        }

        $sql = "SELECT document_type, aggregate_id, title, body, route_name, route_parameters, indexed_at FROM wms_search_document WHERE tenant_id = :tenantId AND (title LIKE :term OR body LIKE :term)
            UNION ALL SELECT 'product', id, sku, name, NULL, '{}', created_at FROM wms_product_reference WHERE tenant_id = :tenantId AND (sku LIKE :term OR name LIKE :term)
            UNION ALL SELECT 'outbound_order', id, order_number, customer_reference, NULL, '{}', created_at FROM wms_outbound_order WHERE tenant_id = :tenantId AND (order_number LIKE :term OR customer_reference LIKE :term)
            UNION ALL SELECT 'inbound_delivery', id, code, delivery_note, NULL, '{}', created_at FROM wms_inbound_delivery WHERE tenant_id = :tenantId AND (code LIKE :term OR delivery_note LIKE :term)
            UNION ALL SELECT 'shipment', id, shipment_number, CONCAT(carrier, ' ', service, ' ', COALESCE(tracking_number, '')), NULL, '{}', created_at FROM wms_shipment WHERE tenant_id = :tenantId AND (shipment_number LIKE :term OR tracking_number LIKE :term)
            ORDER BY indexed_at DESC LIMIT 100";

        return $this->connection->fetchAllAssociative($sql, ['tenantId' => $tenantId, 'term' => '%' . $term . '%']);
    }

    /** @return array<string, mixed>|null */
    public function media(string $tenantId, string $id): ?array
    {
        $row = $this->connection->fetchAssociative('SELECT filename, mime_type, content FROM wms_media_asset WHERE id = :id AND tenant_id = :tenantId', ['id' => $id, 'tenantId' => $tenantId]);

        return $row === false ? null : $row;
    }

    /** @return array<string, mixed>|null */
    public function partnerMedia(string $tenantId, string $userId, string $id): ?array
    {
        $row = $this->connection->fetchAssociative("SELECT m.filename, m.mime_type, m.content FROM wms_media_asset m INNER JOIN wms_partner_account a ON a.business_partner_id = m.aggregate_id AND a.tenant_id = m.tenant_id WHERE m.id = :id AND m.tenant_id = :tenantId AND m.aggregate_type = 'business_partner' AND a.user_id = :userId AND a.active = 1 AND JSON_CONTAINS(a.permissions, '\"media.read\"')", ['id' => $id, 'tenantId' => $tenantId, 'userId' => $userId]);

        return $row === false ? null : $row;
    }

    public function routePrinter(string $tenantId, string $documentType, ?string $siteId, ?string $workstation, ?string $processKey): string
    {
        $printer = $this->connection->fetchOne('SELECT printer_id FROM wms_print_routing_rule WHERE tenant_id = :tenantId AND document_type = :documentType AND active = 1 AND (site_id IS NULL OR site_id = :siteId) AND (workstation IS NULL OR workstation = :workstation) AND (process_key IS NULL OR process_key = :processKey) ORDER BY priority, code LIMIT 1', ['tenantId' => $tenantId, 'documentType' => $documentType, 'siteId' => $siteId, 'workstation' => $workstation, 'processKey' => $processKey]);
        if (!is_string($printer)) {
            throw new \DomainException('Keine aktive Druckroutingregel passt zum Dokument.');
        }

        return $printer;
    }

    /** @return list<array<string, mixed>> */
    private function kpis(string $tenantId): array
    {
        $definitions = $this->all('wms_kpi_definition', $tenantId, 'code');
        $queries = [
            'open_shopfloor_tasks' => "SELECT COUNT(*) FROM wms_shopfloor_task WHERE tenant_id = :tenantId AND status NOT IN ('completed', 'cancelled')",
            'stock_quantity' => 'SELECT COALESCE(SUM(quantity), 0) FROM wms_stock_balance WHERE tenant_id = :tenantId',
            'open_outbound_orders' => "SELECT COUNT(*) FROM wms_outbound_order WHERE tenant_id = :tenantId AND status IN ('imported', 'released')",
            'open_inbound_deliveries' => "SELECT COUNT(*) FROM wms_inbound_delivery WHERE tenant_id = :tenantId AND status NOT IN ('completed', 'cancelled')",
            'queued_print_jobs' => "SELECT COUNT(*) FROM wms_print_job WHERE tenant_id = :tenantId AND status IN ('queued', 'failed')",
        ];
        foreach ($definitions as &$definition) {
            $metric = $definition['metric'] ?? null;
            $sql = is_string($metric) ? ($queries[$metric] ?? null) : null;
            $definition['value'] = $sql === null ? null : $this->connection->fetchOne($sql, ['tenantId' => $tenantId]);
        }
        unset($definition);

        return $definitions;
    }

    private function bill(string $tenantId, string $actorId, string $partnerId, string $type, string $reference, string $description, float $quantity, float $unitPrice, string $currency, DateTimeImmutable $now): string
    {
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_billable_line', ['id' => $id, 'tenant_id' => $tenantId, 'business_partner_id' => $partnerId, 'source_type' => $type, 'source_reference' => trim($reference), 'description' => $description, 'quantity' => $quantity, 'unit_price' => $unitPrice, 'amount' => round($quantity * $unitPrice, 4), 'currency' => $currency, 'service_date' => $now->format('Y-m-d'), 'status' => 'open', 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    /** @return array<string, array{table: string, fields: array<string, string>, references: array<string, string>}> */
    private static function definitions(): array
    {
        return [
            'task' => ['table' => 'wms_shopfloor_task', 'fields' => ['code' => 'code:50', 'task_type' => 'choice:putaway,retrieval,transport,inventory', 'title' => 'string:150', 'reference_type' => 'nullable:40', 'reference_id' => 'nullable:36', 'priority' => 'int:1,999', 'planned_for' => 'nullable:30', 'assigned_to' => 'nullable:36'], 'references' => ['assigned_to' => 'wms_user_account']],
            'kpi' => ['table' => 'wms_kpi_definition', 'fields' => ['code' => 'code:50', 'name' => 'string:120', 'metric' => 'choice:open_shopfloor_tasks,stock_quantity,open_outbound_orders,open_inbound_deliveries,queued_print_jobs', 'aggregation' => 'choice:count,sum,average', 'target_value' => 'nullable_decimal', 'active' => 'bool'], 'references' => []],
            'dashboard' => ['table' => 'wms_dashboard', 'fields' => ['code' => 'code:50', 'name' => 'string:120', 'layout_json' => 'json'], 'references' => []],
            'partner_account' => ['table' => 'wms_partner_account', 'fields' => ['business_partner_id' => 'string:36', 'user_id' => 'string:36', 'permissions' => 'json', 'active' => 'bool'], 'references' => ['business_partner_id' => 'wms_business_partner', 'user_id' => 'wms_user_account']],
            'automation_rule' => ['table' => 'wms_automation_rule', 'fields' => ['code' => 'code:50', 'name' => 'string:120', 'event_name' => 'string:100', 'conditions_json' => 'json', 'action_type' => 'choice:print,notification,follow_up_task', 'action_config' => 'json', 'active' => 'bool'], 'references' => []],
            'storage_fee_rule' => ['table' => 'wms_storage_fee_rule', 'fields' => ['business_partner_id' => 'string:36', 'code' => 'code:50', 'name' => 'string:120', 'price_per_unit_day' => 'decimal', 'free_days' => 'int:0,3650', 'currency' => 'currency', 'active' => 'bool'], 'references' => ['business_partner_id' => 'wms_business_partner']],
            'service' => ['table' => 'wms_value_added_service', 'fields' => ['code' => 'code:50', 'name' => 'string:120', 'unit' => 'string:30', 'unit_price' => 'decimal', 'currency' => 'currency', 'active' => 'bool'], 'references' => []],
            'print_route' => ['table' => 'wms_print_routing_rule', 'fields' => ['code' => 'code:50', 'name' => 'string:120', 'document_type' => 'string:40', 'site_id' => 'nullable:36', 'workstation' => 'nullable:80', 'process_key' => 'nullable:100', 'printer_id' => 'string:36', 'priority' => 'int:1,999', 'active' => 'bool'], 'references' => ['site_id' => 'wms_site', 'printer_id' => 'wms_printer']],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function all(string $table, string $tenantId, string $order): array
    {
        return $this->connection->fetchAllAssociative(sprintf('SELECT * FROM %s WHERE tenant_id = :tenantId ORDER BY %s', $table, $order), ['tenantId' => $tenantId]);
    }

    private function value(string $field, string $type, mixed $value): string|int|float|null
    {
        if ($type === 'bool') {
            return in_array($value, [true, 1, '1', 'true', 'on'], true) ? 1 : 0;
        }
        if ($type === 'json') {
            $json = is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR);
            json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            return $json;
        }
        if ($type === 'decimal' || $type === 'nullable_decimal') {
            if ($type === 'nullable_decimal' && ($value === null || trim((string) $value) === '')) {
                return null;
            }
            if (!is_numeric($value) || (float) $value < 0) {
                throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält keinen gültigen Betrag.', $field));
            }

            return round((float) $value, 4);
        }
        if ($type === 'currency') {
            $currency = strtoupper(trim((string) $value));
            if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
                throw new \InvalidArgumentException('Die Währung muss ein dreistelliger ISO-Code sein.');
            }

            return $currency;
        }
        [$kind, $options] = array_pad(explode(':', $type, 2), 2, '');
        if ($kind === 'nullable') {
            $text = trim((string) $value);

            return $text === '' ? null : $this->required($field, $text, (int) $options);
        }
        if ($kind === 'int') {
            [$min, $max] = array_map('intval', explode(',', $options));
            $integer = filter_var($value, FILTER_VALIDATE_INT);
            if (!is_int($integer) || $integer < $min || $integer > $max) {
                throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält keine gültige Ganzzahl.', $field));
            }

            return $integer;
        }
        if ($kind === 'choice') {
            if (!in_array($value, explode(',', $options), true)) {
                throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält einen ungültigen Wert.', $field));
            }

            return $value;
        }
        if ($kind === 'code') {
            $code = strtolower(trim((string) $value));
            if (preg_match('/^[a-z0-9][a-z0-9._-]*$/', $code) !== 1) {
                throw new \InvalidArgumentException('Codes enthalten nur Kleinbuchstaben, Zahlen, Punkt, Unterstrich und Bindestrich.');
            }

            return $this->required($field, $code, (int) $options);
        }

        return $this->required($field, (string) $value, (int) $options);
    }

    private function assertReference(string $tenantId, string $table, string $id, string $field): void
    {
        if ($this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id AND tenant_id = :tenantId', $table), ['id' => $id, 'tenantId' => $tenantId]) === false) {
            throw new \InvalidArgumentException(sprintf('Die Referenz "%s" gehört nicht zum Mandanten.', $field));
        }
    }

    private function required(string $field, string $value, int $maximum): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > $maximum) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich oder zu lang.', $field));
        }

        return $value;
    }

    private function audit(Connection $connection, string $tenantId, string $actorId, string $type, string $id, string $event, array $payload, DateTimeImmutable $now): void
    {
        $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => 'platform.' . $type, 'aggregate_id' => $id, 'event_type' => $event, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $this->date($now)]);
    }

    private function index(Connection $connection, string $tenantId, string $type, string $id, string $title, string $body, DateTimeImmutable $now): void
    {
        $connection->insert('wms_search_document', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'document_type' => $type, 'aggregate_id' => $id, 'title' => $title, 'body' => $body, 'route_name' => 'v3_platform_index', 'route_parameters' => '{}', 'indexed_at' => $this->date($now)]);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
