<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application\Query;

use Doctrine\DBAL\Connection;
use LogicException;

readonly class IntegrationQueryService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function exchangeJobs(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, direction, format, resource_type, source_reference, status, row_count, error_message, '
            . 'created_by, created_at, completed_at FROM wms_integration_job '
            . 'WHERE tenant_id = :tenantId ORDER BY created_at DESC, id DESC',
            ['tenantId' => $tenantId],
        );
    }

    public function integrationMappings(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, system_type, message_type, source_field, target_field, transformation, created_by, created_at '
            . 'FROM wms_integration_mapping WHERE tenant_id = :tenantId '
            . 'ORDER BY system_type, message_type, source_field',
            ['tenantId' => $tenantId],
        );
    }

    public function commerceConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, channel_type, endpoint_url, credential_env, active, created_by, created_at '
            . 'FROM wms_commerce_connection WHERE tenant_id = :tenantId ORDER BY channel_type, name, id',
            ['tenantId' => $tenantId],
        );
    }

    public function channelOrders(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT o.id, o.connection_id, c.name connection_name, o.external_order_id, o.status, '
            . 'o.imported_by, o.imported_at FROM wms_channel_order o '
            . 'JOIN wms_commerce_connection c ON c.id = o.connection_id AND c.tenant_id = o.tenant_id '
            . 'WHERE o.tenant_id = :tenantId ORDER BY o.imported_at DESC, o.id DESC',
            ['tenantId' => $tenantId],
        );
    }

    public function erpConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_erp_connection WHERE tenant_id = :tenantId ORDER BY name, id',
            ['tenantId' => $tenantId],
        );
    }

    public function erpConnection(string $tenantId, string $connectionId): ?array
    {
        $connection = $this->connection->fetchAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_erp_connection WHERE id = :connectionId AND tenant_id = :tenantId',
            ['connectionId' => $connectionId, 'tenantId' => $tenantId],
        );

        return $connection === false ? null : $connection;
    }

    public function outboxMessages(string $tenantId, string $status, int $limit, ?string $cursor): array
    {
        $messages = $this->connection->fetchAllAssociative(
            'SELECT id, event_name, aggregate_type, aggregate_id, payload, status, occurred_at, created_by, '
            . 'attempt_count, next_attempt_at, published_at, last_error, retried_by, retried_at '
            . 'FROM wms_integration_outbox WHERE tenant_id = :tenantId AND status = :status '
            . 'AND (:cursorFilter IS NULL OR id > :cursorValue) ORDER BY id LIMIT ' . $limit,
            ['tenantId' => $tenantId, 'status' => $status, 'cursorFilter' => $cursor, 'cursorValue' => $cursor ?? ''],
        );

        return array_map($this->decodeOutboxPayload(...), $messages);
    }

    public function outboxMessage(string $tenantId, string $messageId): ?array
    {
        $message = $this->connection->fetchAssociative(
            'SELECT id, event_name, aggregate_type, aggregate_id, payload, status, occurred_at, '
            . 'created_by, acknowledged_by, acknowledged_at, attempt_count, next_attempt_at, claimed_at, '
            . 'published_at, last_error, retried_by, retried_at '
            . 'FROM wms_integration_outbox WHERE id = :messageId AND tenant_id = :tenantId',
            ['messageId' => $messageId, 'tenantId' => $tenantId],
        );

        return $message === false ? null : $this->decodeOutboxPayload($message);
    }

    public function carrierConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, carrier_code, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_carrier_connection WHERE tenant_id = :tenantId ORDER BY carrier_code, name, id',
            ['tenantId' => $tenantId],
        );
    }

    public function carrierConnection(string $tenantId, string $connectionId): ?array
    {
        $connection = $this->connection->fetchAssociative(
            'SELECT id, name, carrier_code, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_carrier_connection WHERE id = :id AND tenant_id = :tenantId',
            ['id' => $connectionId, 'tenantId' => $tenantId],
        );

        return $connection === false ? null : $connection;
    }

    public function printers(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_printer WHERE tenant_id = :tenantId ORDER BY name, id',
            ['tenantId' => $tenantId],
        );
    }

    public function printer(string $tenantId, string $printerId): ?array
    {
        $printer = $this->connection->fetchAssociative(
            'SELECT id, name, endpoint_url, credential_env, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_printer WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $printerId],
        );

        return $printer === false ? null : $printer;
    }

    public function printJobs(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT j.id, j.printer_id, p.name printer_name, j.document_type, j.document_reference, '
            . 'j.format, j.copies, j.status, j.attempts, j.external_reference, j.last_error, '
            . 'j.created_by, j.created_at, j.completed_at FROM wms_print_job j '
            . 'INNER JOIN wms_printer p ON p.id = j.printer_id AND p.tenant_id = j.tenant_id '
            . 'WHERE j.tenant_id = :tenantId ORDER BY j.created_at DESC, j.id DESC LIMIT 100',
            ['tenantId' => $tenantId],
        );
    }

    public function printJob(string $tenantId, string $jobId): ?array
    {
        $job = $this->connection->fetchAssociative(
            'SELECT j.id, j.printer_id, p.name printer_name, j.document_type, j.document_reference, '
            . 'j.format, j.copies, j.status, j.attempts, j.external_reference, j.last_error, '
            . 'j.created_by, j.created_at, j.completed_at FROM wms_print_job j '
            . 'INNER JOIN wms_printer p ON p.id = j.printer_id AND p.tenant_id = j.tenant_id '
            . 'WHERE j.tenant_id = :tenantId AND j.id = :id',
            ['tenantId' => $tenantId, 'id' => $jobId],
        );

        return $job === false ? null : $job;
    }

    public function devices(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_device WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    public function device(string $tenantId, string $deviceId): ?array
    {
        $device = $this->connection->fetchAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_device WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );

        return $device === false ? null : $device;
    }

    public function scanEvents(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.device_id, d.code device_code, d.name device_name, e.scan_type, e.scan_value, '
            . 'e.process_type, e.context_reference, e.request_id, e.status, e.message, '
            . 'e.scanned_by, e.scanned_at FROM wms_scan_event e '
            . 'INNER JOIN wms_device d ON d.id = e.device_id AND d.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId ORDER BY e.scanned_at DESC, e.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    public function scanEvent(string $tenantId, string $eventId): ?array
    {
        $event = $this->connection->fetchAssociative(
            'SELECT e.id, e.device_id, d.code device_code, d.name device_name, e.scan_type, e.scan_value, '
            . 'e.process_type, e.context_reference, e.request_id, e.status, e.message, '
            . 'e.scanned_by, e.scanned_at FROM wms_scan_event e '
            . 'INNER JOIN wms_device d ON d.id = e.device_id AND d.tenant_id = e.tenant_id '
            . 'WHERE e.tenant_id = :tenantId AND e.id = :id',
            ['tenantId' => $tenantId, 'id' => $eventId],
        );

        return $event === false ? null : $event;
    }

    public function measurementDevices(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_measurement_device WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    public function measurementDevice(string $tenantId, string $deviceId): ?array
    {
        $device = $this->connection->fetchAssociative(
            'SELECT id, code, name, device_type, active, created_by, created_at, changed_by, changed_at '
            . 'FROM wms_measurement_device WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );

        return $device === false ? null : $device;
    }

    public function measurements(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT m.id, m.device_id, d.code device_code, d.name device_name, m.target_type, m.target_id, '
            . 'm.weight_grams, m.length_mm, m.width_mm, m.height_mm, m.request_id, m.status, m.message, '
            . 'm.measured_by, m.measured_at FROM wms_measurement m '
            . 'INNER JOIN wms_measurement_device d ON d.id = m.device_id AND d.tenant_id = m.tenant_id '
            . 'WHERE m.tenant_id = :tenantId ORDER BY m.measured_at DESC, m.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    public function measurement(string $tenantId, string $measurementId): ?array
    {
        $measurement = $this->connection->fetchAssociative(
            'SELECT m.id, m.device_id, d.code device_code, d.name device_name, m.target_type, m.target_id, '
            . 'm.weight_grams, m.length_mm, m.width_mm, m.height_mm, m.request_id, m.status, m.message, '
            . 'm.measured_by, m.measured_at FROM wms_measurement m '
            . 'INNER JOIN wms_measurement_device d ON d.id = m.device_id AND d.tenant_id = m.tenant_id '
            . 'WHERE m.tenant_id = :tenantId AND m.id = :id',
            ['tenantId' => $tenantId, 'id' => $measurementId],
        );

        return $measurement === false ? null : $measurement;
    }

    public function measurablePackages(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT p.id, p.package_number, o.code packing_order_code, p.weight_grams, '
            . 'p.length_mm, p.width_mm, p.height_mm FROM wms_package p '
            . 'INNER JOIN wms_packing_order o ON o.id = p.packing_order_id '
            . "WHERE o.tenant_id = :tenantId AND o.status IN ('open', 'packing') ORDER BY o.code, p.package_number",
            ['tenantId' => $tenantId],
        );
    }

    public function automationDevices(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, device_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_automation_device WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    public function automationDevice(string $tenantId, string $deviceId): ?array
    {
        $device = $this->connection->fetchAssociative(
            'SELECT id, code, name, device_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_automation_device WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $deviceId],
        );

        return $device === false ? null : $device;
    }

    public function deviceCommands(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT c.id, c.device_id, d.code device_code, d.name device_name, c.command_type, c.location_id, '
            . 'l.code location_code, c.reference_type, c.reference_id, c.request_id, c.status, c.message, '
            . 'c.created_by, c.created_at, c.changed_by, c.changed_at FROM wms_device_command c '
            . 'INNER JOIN wms_automation_device d ON d.id = c.device_id AND d.tenant_id = c.tenant_id '
            . 'INNER JOIN wms_storage_location l ON l.id = c.location_id AND l.tenant_id = c.tenant_id '
            . 'WHERE c.tenant_id = :tenantId ORDER BY c.created_at DESC, c.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    public function deviceCommand(string $tenantId, string $commandId): ?array
    {
        $command = $this->connection->fetchAssociative(
            'SELECT c.id, c.device_id, d.code device_code, d.name device_name, c.command_type, c.location_id, '
            . 'l.code location_code, c.reference_type, c.reference_id, c.request_id, c.status, c.message, '
            . 'c.created_by, c.created_at, c.changed_by, c.changed_at FROM wms_device_command c '
            . 'INNER JOIN wms_automation_device d ON d.id = c.device_id AND d.tenant_id = c.tenant_id '
            . 'INNER JOIN wms_storage_location l ON l.id = c.location_id AND l.tenant_id = c.tenant_id '
            . 'WHERE c.tenant_id = :tenantId AND c.id = :id',
            ['tenantId' => $tenantId, 'id' => $commandId],
        );

        return $command === false ? null : $command;
    }

    public function automationLocations(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT l.id, l.code, w.code warehouse_code FROM wms_storage_location l '
            . 'INNER JOIN wms_warehouse w ON w.id = l.warehouse_id AND w.tenant_id = l.tenant_id '
            . 'WHERE l.tenant_id = :tenantId ORDER BY w.code, l.code',
            ['tenantId' => $tenantId],
        );
    }

    public function wcsConnections(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, code, name, system_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_wcs_connection WHERE tenant_id = :tenantId ORDER BY code, id',
            ['tenantId' => $tenantId],
        );
    }

    public function wcsConnection(string $tenantId, string $connectionId): ?array
    {
        $connection = $this->connection->fetchAssociative(
            'SELECT id, code, name, system_type, endpoint_url, credential_env, active, created_by, created_at, '
            . 'changed_by, changed_at FROM wms_wcs_connection WHERE tenant_id = :tenantId AND id = :id',
            ['tenantId' => $tenantId, 'id' => $connectionId],
        );

        return $connection === false ? null : $connection;
    }

    public function machineCommands(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT c.id, c.connection_id, w.code connection_code, c.command_type, c.source, c.destination, '
            . 'c.load_unit, c.request_id, c.status, c.message, c.created_by, c.created_at, c.changed_by, c.changed_at '
            . 'FROM wms_machine_command c INNER JOIN wms_wcs_connection w ON w.id = c.connection_id '
            . 'AND w.tenant_id = c.tenant_id WHERE c.tenant_id = :tenantId ORDER BY c.created_at DESC, c.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    public function machineCommand(string $tenantId, string $commandId): ?array
    {
        $command = $this->connection->fetchAssociative(
            'SELECT c.id, c.connection_id, w.code connection_code, c.command_type, c.source, c.destination, '
            . 'c.load_unit, c.request_id, c.status, c.message, c.created_by, c.created_at, c.changed_by, c.changed_at '
            . 'FROM wms_machine_command c INNER JOIN wms_wcs_connection w ON w.id = c.connection_id '
            . 'AND w.tenant_id = c.tenant_id WHERE c.tenant_id = :tenantId AND c.id = :id',
            ['tenantId' => $tenantId, 'id' => $commandId],
        );

        return $command === false ? null : $command;
    }

    public function machineStatuses(string $tenantId, int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT s.id, s.connection_id, w.code connection_code, s.command_id, s.machine_code, s.status, '
            . 's.message, s.external_event_id, s.recorded_by, s.recorded_at FROM wms_machine_status s '
            . 'INNER JOIN wms_wcs_connection w ON w.id = s.connection_id AND w.tenant_id = s.tenant_id '
            . 'WHERE s.tenant_id = :tenantId ORDER BY s.recorded_at DESC, s.id DESC LIMIT ' . $limit,
            ['tenantId' => $tenantId],
        );
    }

    public function transportEndpoints(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT e.id, e.code, e.name, e.adapter_type, e.address, e.credential_env, e.active, '
            . 'p.protocol, p.framing, p.connect_timeout_ms, p.read_timeout_ms, e.created_by, e.created_at, '
            . 'e.changed_by, e.changed_at FROM wms_transport_endpoint e INNER JOIN wms_protocol_configuration p '
            . 'ON p.endpoint_id = e.id WHERE e.tenant_id = :tenantId ORDER BY e.code, e.id',
            ['tenantId' => $tenantId],
        );
    }

    public function transportEndpoint(string $tenantId, string $endpointId): ?array
    {
        $endpoint = $this->connection->fetchAssociative(
            'SELECT e.id, e.code, e.name, e.adapter_type, e.address, e.credential_env, e.active, '
            . 'p.protocol, p.framing, p.connect_timeout_ms, p.read_timeout_ms, e.created_by, e.created_at, '
            . 'e.changed_by, e.changed_at FROM wms_transport_endpoint e INNER JOIN wms_protocol_configuration p '
            . 'ON p.endpoint_id = e.id WHERE e.tenant_id = :tenantId AND e.id = :id',
            ['tenantId' => $tenantId, 'id' => $endpointId],
        );

        return $endpoint === false ? null : $endpoint;
    }

    /**
     * @param array<string, mixed> $message
     *
     * @return array<string, mixed>
     */
    private function decodeOutboxPayload(array $message): array
    {
        if (!is_string($message['payload'] ?? null)) {
            throw new LogicException('The outbox payload projection is invalid.');
        }

        $payload = json_decode($message['payload'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($payload)) {
            throw new LogicException('The outbox payload must decode to an object.');
        }

        $message['payload'] = $payload;

        return $message;
    }
}
