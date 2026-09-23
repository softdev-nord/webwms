<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class AdministrationWorkspaceService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /** @return array<string, list<array<string, mixed>>> */
    public function workspace(string $tenantId): array
    {
        return [
            'partners' => $this->all('wms_business_partner', $tenantId, 'code'),
            'contexts' => $this->connection->fetchAllAssociative('SELECT c.*, p.code partner_code FROM wms_tenant_context c LEFT JOIN wms_business_partner p ON p.id = c.business_partner_id WHERE c.tenant_id = :tenantId ORDER BY c.code', ['tenantId' => $tenantId]),
            'sites' => $this->all('wms_site', $tenantId, 'code'),
            'warehouses' => $this->connection->fetchAllAssociative('SELECT w.*, s.code site_code FROM wms_warehouse w INNER JOIN wms_site s ON s.id = w.site_id WHERE w.tenant_id = :tenantId ORDER BY s.code, w.code', ['tenantId' => $tenantId]),
            'identityProviders' => $this->all('wms_identity_provider', $tenantId, 'code'),
            'numberRanges' => $this->all('wms_number_range', $tenantId, 'code'),
            'processes' => $this->all('wms_process_configuration', $tenantId, 'process_key'),
            'deviceProfiles' => $this->all('wms_device_profile', $tenantId, 'code'),
            'deployments' => $this->connection->fetchAllAssociative('SELECT * FROM wms_deployment_configuration WHERE tenant_id = :tenantId', ['tenantId' => $tenantId]),
            'events' => $this->connection->fetchAllAssociative('SELECT e.*, u.email performed_by_email FROM wms_administration_event e INNER JOIN wms_user_account u ON u.id = e.performed_by WHERE e.tenant_id = :tenantId ORDER BY e.occurred_at DESC LIMIT 100', ['tenantId' => $tenantId]),
        ];
    }

    /** @param array<string, mixed> $values */
    public function create(string $tenantId, string $actorId, string $resource, array $values, DateTimeImmutable $now): string
    {
        $definitions = self::definitions();
        if (!isset($definitions[$resource])) {
            throw new \InvalidArgumentException('Die Administrationsressource ist unbekannt.');
        }
        $definition = $definitions[$resource];
        $id = Uuid::v7()->toRfc4122();
        $row = ['id' => $id, 'tenant_id' => $tenantId];
        foreach ($definition['fields'] as $field => $type) {
            $value = $values[$field] ?? null;
            $row[$field] = $this->value($field, $type, $value);
        }
        if ($resource === 'identity_provider' && filter_var($row['issuer_url'], FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Die Issuer-/Metadata-URL ist ungültig.');
        }
        if ($resource === 'context' && $row['business_partner_id'] !== null && $this->connection->fetchOne('SELECT 1 FROM wms_business_partner WHERE id = :id AND tenant_id = :tenantId', ['id' => $row['business_partner_id'], 'tenantId' => $tenantId]) === false) {
            throw new \InvalidArgumentException('Der Geschäftspartner gehört nicht zum Mandanten.');
        }
        $row['created_by'] = $actorId;
        $row['created_at'] = $this->date($now);

        $this->connection->transactional(function (Connection $connection) use ($definition, $row, $tenantId, $actorId, $resource, $id, $now): void {
            $connection->insert($definition['table'], $row);
            $this->audit($connection, $tenantId, $actorId, $resource, $id, 'created', $row, $now);
        });

        return $id;
    }

    public function setEnabled(string $tenantId, string $actorId, string $resource, string $id, bool $enabled, DateTimeImmutable $now): void
    {
        $definitions = self::definitions();
        if (!isset($definitions[$resource]) || (!array_key_exists('enabled', $definitions[$resource]['fields']) && !array_key_exists('active', $definitions[$resource]['fields']))) {
            throw new \InvalidArgumentException('Die Ressource kann nicht geschaltet werden.');
        }
        $field = array_key_exists('enabled', $definitions[$resource]['fields']) ? 'enabled' : 'active';
        $this->connection->transactional(function (Connection $connection) use ($definitions, $resource, $field, $enabled, $tenantId, $id, $actorId, $now): void {
            $updated = $connection->update($definitions[$resource]['table'], [$field => $enabled ? 1 : 0, 'changed_by' => $actorId, 'changed_at' => $this->date($now)], ['id' => $id, 'tenant_id' => $tenantId]);
            if ($updated !== 1) {
                throw new \InvalidArgumentException('Der Datensatz wurde im Mandanten nicht gefunden.');
            }
            $this->audit($connection, $tenantId, $actorId, $resource, $id, $enabled ? 'enabled' : 'disabled', [], $now);
        });
    }

    public function configureProcess(string $tenantId, string $actorId, string $key, string $name, bool $enabled, string $configuration, DateTimeImmutable $now): void
    {
        $key = $this->code($key, 100);
        $configuration = trim($configuration) === '' ? '{}' : $configuration;
        json_decode($configuration, true, 512, JSON_THROW_ON_ERROR);
        $existing = $this->connection->fetchOne('SELECT id FROM wms_process_configuration WHERE tenant_id = :tenantId AND process_key = :processKey', ['tenantId' => $tenantId, 'processKey' => $key]);
        $id = is_string($existing) ? $existing : Uuid::v7()->toRfc4122();
        $data = ['name' => $this->required('name', $name, 150), 'enabled' => $enabled ? 1 : 0, 'configuration' => $configuration, 'changed_by' => $actorId, 'changed_at' => $this->date($now)];
        $this->connection->transactional(function (Connection $connection) use ($existing, $id, $tenantId, $key, $data, $actorId, $now): void {
            if ($existing === false) {
                $connection->insert('wms_process_configuration', ['id' => $id, 'tenant_id' => $tenantId, 'process_key' => $this->code($key, 100)] + $data);
            } else {
                $connection->update('wms_process_configuration', $data, ['id' => $id, 'tenant_id' => $tenantId]);
            }
            $this->audit($connection, $tenantId, $actorId, 'process', $id, 'configured', ['process_key' => $key, 'enabled' => $data['enabled']], $now);
        });
    }

    /** @param array<string, string> $values */
    public function configureDeployment(string $tenantId, string $actorId, array $values, DateTimeImmutable $now): void
    {
        $data = [
            'deployment_mode' => $this->choice('deployment_mode', $values['deployment_mode'] ?? '', ['saas', 'on_premises', 'hybrid']),
            'public_url' => $this->required('public_url', $values['public_url'] ?? '', 500),
            'storage_driver' => $this->choice('storage_driver', $values['storage_driver'] ?? '', ['local', 's3', 'azure']),
            'queue_transport' => $this->choice('queue_transport', $values['queue_transport'] ?? '', ['sync', 'doctrine', 'rabbitmq']),
            'release_channel' => $this->choice('release_channel', $values['release_channel'] ?? '', ['stable', 'preview']),
            'changed_by' => $actorId,
            'changed_at' => $this->date($now),
        ];
        if (filter_var($data['public_url'], FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Die öffentliche URL ist ungültig.');
        }
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $data, $actorId, $now): void {
            if ($connection->fetchOne('SELECT 1 FROM wms_deployment_configuration WHERE tenant_id = :tenantId', ['tenantId' => $tenantId]) === false) {
                $connection->insert('wms_deployment_configuration', ['tenant_id' => $tenantId] + $data);
            } else {
                $connection->update('wms_deployment_configuration', $data, ['tenant_id' => $tenantId]);
            }
            $this->audit($connection, $tenantId, $actorId, 'deployment', $tenantId, 'configured', $data, $now);
        });
    }

    public function nextNumber(string $tenantId, string $actorId, string $code, DateTimeImmutable $now): string
    {
        return $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $code, $now): string {
            $range = $connection->fetchAssociative('SELECT * FROM wms_number_range WHERE tenant_id = :tenantId AND code = :code AND enabled = 1 FOR UPDATE', ['tenantId' => $tenantId, 'code' => $code]);
            if ($range === false) {
                throw new \InvalidArgumentException('Der aktive Nummernkreis wurde nicht gefunden.');
            }
            $next = (int) $range['next_value'];
            if ($range['maximum_value'] !== null && $next > (int) $range['maximum_value']) {
                throw new \DomainException('Der Nummernkreis ist ausgeschöpft.');
            }
            $connection->update('wms_number_range', ['next_value' => $next + 1, 'changed_by' => $actorId, 'changed_at' => $this->date($now)], ['id' => $range['id'], 'tenant_id' => $tenantId]);
            $number = (string) $range['prefix'] . str_pad((string) $next, (int) $range['padding'], '0', STR_PAD_LEFT) . (string) $range['suffix'];
            $this->audit($connection, $tenantId, $actorId, 'number_range', (string) $range['id'], 'number_allocated', ['number' => $number], $now);

            return $number;
        });
    }

    /** @return array<string, array{table: string, fields: array<string, string>}> */
    private static function definitions(): array
    {
        return [
            'partner' => ['table' => 'wms_business_partner', 'fields' => ['code' => 'code:30', 'name' => 'string:150', 'partner_type' => 'choice:customer,supplier,carrier,owner', 'external_reference' => 'nullable:100', 'active' => 'bool']],
            'context' => ['table' => 'wms_tenant_context', 'fields' => ['business_partner_id' => 'nullable:36', 'code' => 'code:30', 'name' => 'string:150', 'active' => 'bool']],
            'identity_provider' => ['table' => 'wms_identity_provider', 'fields' => ['code' => 'code:30', 'name' => 'string:100', 'protocol' => 'choice:oidc,saml', 'issuer_url' => 'string:500', 'client_id' => 'string:255', 'client_secret_env' => 'string:100', 'scopes' => 'string:255', 'enabled' => 'bool']],
            'number_range' => ['table' => 'wms_number_range', 'fields' => ['code' => 'code:30', 'name' => 'string:100', 'object_type' => 'code:50', 'prefix' => 'nullable:30', 'suffix' => 'nullable:30', 'padding' => 'int:1,18', 'next_value' => 'int:1,9223372036854775807', 'maximum_value' => 'nullable_int', 'gs1_company_prefix' => 'nullable:20', 'enabled' => 'bool']],
            'device_profile' => ['table' => 'wms_device_profile', 'fields' => ['code' => 'code:30', 'name' => 'string:100', 'device_type' => 'choice:desktop,tablet,scanner', 'start_route' => 'string:255', 'fullscreen' => 'bool', 'scan_suffix' => 'nullable:20', 'enabled' => 'bool']],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function all(string $table, string $tenantId, string $order): array
    {
        return $this->connection->fetchAllAssociative(sprintf('SELECT * FROM %s WHERE tenant_id = :tenantId ORDER BY %s', $table, $order), ['tenantId' => $tenantId]);
    }

    private function value(string $field, string $type, mixed $value): string|int|null
    {
        if ($type === 'bool') {
            return in_array($value, [true, 1, '1', 'true', 'on'], true) ? 1 : 0;
        }
        if ($type === 'nullable_int') {
            return $value === null || trim((string) $value) === '' ? null : $this->integer($field, $value, 1, PHP_INT_MAX);
        }
        [$kind, $options] = array_pad(explode(':', $type, 2), 2, '');
        if ($kind === 'nullable') {
            $text = trim((string) $value);

            return $text === '' ? null : $this->required($field, $text, (int) $options);
        }
        if ($kind === 'int') {
            [$minimum, $maximum] = array_map('intval', explode(',', $options));

            return $this->integer($field, $value, $minimum, $maximum);
        }
        if ($kind === 'choice') {
            return $this->choice($field, (string) $value, explode(',', $options));
        }
        if ($kind === 'code') {
            return $this->code((string) $value, (int) $options);
        }

        return $this->required($field, (string) $value, (int) $options);
    }

    private function required(string $field, string $value, int $maximum): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) > $maximum) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich und darf höchstens %d Zeichen enthalten.', $field, $maximum));
        }

        return $value;
    }

    private function code(string $value, int $maximum): string
    {
        $value = strtolower(trim($value));
        if (preg_match('/^[a-z0-9][a-z0-9._-]*$/', $value) !== 1) {
            throw new \InvalidArgumentException('Codes dürfen nur Kleinbuchstaben, Zahlen, Punkt, Unterstrich und Bindestrich enthalten.');
        }

        return $this->required('code', $value, $maximum);
    }

    /** @param list<string> $choices */
    private function choice(string $field, string $value, array $choices): string
    {
        if (!in_array($value, $choices, true)) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält einen ungültigen Wert.', $field));
        }

        return $value;
    }

    private function integer(string $field, mixed $value, int $minimum, int $maximum): int
    {
        $integer = filter_var($value, FILTER_VALIDATE_INT);
        if (!is_int($integer) || $integer < $minimum || $integer > $maximum) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält keine gültige Ganzzahl.', $field));
        }

        return $integer;
    }

    /** @param array<string, mixed> $payload */
    private function audit(Connection $connection, string $tenantId, string $actorId, string $type, string $id, string $event, array $payload, DateTimeImmutable $now): void
    {
        unset($payload['client_secret_env']);
        $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => $type, 'aggregate_id' => $id, 'event_type' => $event, 'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $this->date($now)]);
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
