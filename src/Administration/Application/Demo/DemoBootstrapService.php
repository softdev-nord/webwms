<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Demo;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use WebWMS\Administration\Application\Access\V3PermissionCatalog;
use WebWMS\Administration\Domain\Access\PasswordHasher;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Site\SiteId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\OutboundOrder;
use WebWMS\Inventory\Domain\OutboundOrderItem;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\Sku;
use WebWMS\Inventory\Domain\StockPosting;
use WebWMS\Inventory\Domain\StorageLocation;
use WebWMS\Inventory\Domain\Warehouse;

final readonly class DemoBootstrapService
{
    public const string TENANT_ID = '83b4f6dd-4ef6-4226-9081-e6a2cedf14dd';

    public const string SITE_ID = '91c8a9f7-ddd8-418a-b9bc-f2f85b95cd53';

    public const string ROLE_ID = '8ef53de0-1abf-4a8a-8f89-f0f67cf3f14f';

    public const string USER_ID = '8f7239e6-48a1-45f5-a55f-1e4c95acd8d2';

    public const string WAREHOUSE_ID = 'f0f8a7ee-ef95-4a8a-b68b-96f0d864f4ca';

    public const string LOCATION_A_ID = 'fd1ea338-9691-447f-a44e-b6f577ec07c6';

    public const string LOCATION_B_ID = 'a3f696d9-fac9-4b06-a0f4-33f76e9c7d9d';

    public const string AREA_ID = '80dc3871-a751-45f4-b1a0-6261ad471783';

    public const string AISLE_ID = 'b059382c-6e64-4302-8fb8-c357617fba28';

    public const string PRODUCT_A_ID = 'af7b6174-a744-4346-a243-6f5e59e1de4f';

    public const string PRODUCT_B_ID = 'dcfbe1d5-7210-4f28-afc7-e098214f102f';

    public const string ORDER_ID = '3b190f7e-aa8d-412f-bf6e-a102d99d2b72';

    public const string ORDER_ITEM_ID = '5f55fe9e-2396-4861-8dc8-a3b6088d53fc';

    public const string PURCHASE_ORDER_ID = 'eb4bdfea-1f02-4dbd-bedd-b063fc92d817';

    public const string PURCHASE_ORDER_ITEM_ID = '3563d5f3-16af-4bb1-b4f9-9a23d6ca318a';

    public const string INBOUND_DELIVERY_ID = 'a0990777-d307-41c3-95d3-f124a5f3312e';

    public const string INBOUND_DELIVERY_LINE_ID = 'dc37b2b3-996f-4a57-bb13-ad07eb8b1994';

    public const string PUTAWAY_STRATEGY_ID = 'd22003b0-65dc-4f74-9efc-dcbe8db7b963';

    public const string PRINTER_ID = 'd1eac343-b344-4ea1-9f68-6e6303e0f3f9';

    public const string DEVICE_ID = 'e2a71554-20cb-4a79-a5cc-f65215d2ca21';

    public const string MEASUREMENT_DEVICE_ID = 'f2d82665-29dc-4d98-8c91-c2e82504f1d4';

    public const string AUTOMATION_DEVICE_ID = 'a67562f8-9eb5-4b7f-a93b-39132c7d0e11';

    public const string WCS_CONNECTION_ID = '89c3f8dc-19a0-48b5-911f-601ba20802cb';

    public const string TRANSPORT_ENDPOINT_ID = '7414cf86-e29a-43d3-8141-2892e302991f';

    public const string BUSINESS_PARTNER_ID = '50c31f4f-fde8-4a4b-b2cf-706a8be00d43';

    public const string TENANT_CONTEXT_ID = '88993899-6006-484f-b4ad-aa9cc63147a1';

    public const string NUMBER_RANGE_ID = '41864d2c-3227-43f8-b490-03c3a7e2e94d';

    public const string DEVICE_PROFILE_ID = '040d919b-aae9-4103-a3c8-1a40c0b257d9';

    public const string FORKLIFT_ID = '0b117a0e-ea4a-4fe3-8c4c-80584601d5d4';

    public const string TRANSPORT_RULE_ID = '379b8977-f3e1-42b2-b3d4-94dff79d3953';

    public const string STATION_A_ID = '66c0c73e-f00f-4346-a4e6-761cb4992680';

    public const string STATION_B_ID = '1d70f3f9-196d-4831-b7ae-2c3e74164622';

    public const string MILK_RUN_ID = '52d49360-b891-46f8-a091-4063bd674b58';

    public const string EMAIL = 'admin@demo.webwms.local';

    public function __construct(
        private Connection $connection,
        private PasswordHasher $passwordHasher,
        private InventoryRepository $inventory,
        private ExtendedDemoDatasetService $extendedDataset,
    ) {
    }

    public function bootstrap(?string $plainPassword = null): DemoBootstrapResult
    {
        $now = new DateTimeImmutable();
        $generatedPassword = null;
        $created = false;

        if (!$this->exists('wms_tenant', self::TENANT_ID)) {
            $this->connection->insert('wms_tenant', [
                'id' => self::TENANT_ID, 'name' => 'WebWMS Demo Mandant', 'status' => 'active',
                'created_at' => $this->date($now), 'updated_at' => $this->date($now),
            ]);
            $created = true;
        }
        if (!$this->exists('wms_site', self::SITE_ID)) {
            $this->connection->insert('wms_site', [
                'id' => self::SITE_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'DEMO',
                'name' => 'Demo-Standort', 'timezone' => 'Europe/Berlin', 'status' => 'active',
                'created_at' => $this->date($now), 'updated_at' => $this->date($now),
            ]);
        }
        if (!$this->exists('wms_role', self::ROLE_ID)) {
            $this->connection->insert('wms_role', [
                'id' => self::ROLE_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'ROLE_ADMIN',
                'name' => 'Demo-Administrator', 'created_at' => $this->date($now), 'updated_at' => $this->date($now),
            ]);
        }
        foreach (V3PermissionCatalog::ALL as $permission) {
            if ($this->connection->fetchOne(
                'SELECT 1 FROM wms_role_permission WHERE role_id = :roleId AND permission_key = :permission',
                ['roleId' => self::ROLE_ID, 'permission' => $permission],
            ) === false) {
                $this->connection->insert('wms_role_permission', ['role_id' => self::ROLE_ID, 'permission_key' => $permission]);
            }
        }
        if (!$this->exists('wms_user_account', self::USER_ID)) {
            $password = $plainPassword ?? $this->generatePassword();
            $this->connection->insert('wms_user_account', [
                'id' => self::USER_ID, 'tenant_id' => self::TENANT_ID, 'email' => self::EMAIL,
                'display_name' => 'Demo Administrator', 'password_hash' => $this->passwordHasher->hash($password),
                'status' => 'active', 'created_at' => $this->date($now), 'updated_at' => $this->date($now),
            ]);
            $generatedPassword = $plainPassword === null ? $password : null;
            $created = true;
        }
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_role WHERE user_id = :userId AND role_id = :roleId',
            ['userId' => self::USER_ID, 'roleId' => self::ROLE_ID],
        ) === false) {
            $this->connection->insert('wms_user_role', ['user_id' => self::USER_ID, 'role_id' => self::ROLE_ID]);
        }
        $this->createAdministrationDemo($now);
        if (!$this->exists('wms_printer', self::PRINTER_ID)) {
            $this->connection->insert('wms_printer', [
                'id' => self::PRINTER_ID,
                'tenant_id' => self::TENANT_ID,
                'name' => 'Demo ZPL Drucker',
                'endpoint_url' => 'https://printer.demo.webwms.local/print',
                'credential_env' => 'DEMO_PRINTER_TOKEN',
                'active' => 1,
                'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
                'changed_by' => null,
                'changed_at' => null,
            ]);
        }
        if (!$this->exists('wms_device', self::DEVICE_ID)) {
            $this->connection->insert('wms_device', [
                'id' => self::DEVICE_ID,
                'tenant_id' => self::TENANT_ID,
                'code' => 'MDE-DEMO-01',
                'name' => 'Demo MDE Warenausgang',
                'device_type' => 'mde',
                'active' => 1,
                'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
                'changed_by' => null,
                'changed_at' => null,
            ]);
        }
        if (!$this->exists('wms_measurement_device', self::MEASUREMENT_DEVICE_ID)) {
            $this->connection->insert('wms_measurement_device', [
                'id' => self::MEASUREMENT_DEVICE_ID,
                'tenant_id' => self::TENANT_ID,
                'code' => 'MEASURE-DEMO-01',
                'name' => 'Demo Packplatzwaage mit Volumenmessung',
                'device_type' => 'combined',
                'active' => 1,
                'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
                'changed_by' => null,
                'changed_at' => null,
            ]);
        }
        if (!$this->exists('wms_automation_device', self::AUTOMATION_DEVICE_ID)) {
            $this->connection->insert('wms_automation_device', [
                'id' => self::AUTOMATION_DEVICE_ID,
                'tenant_id' => self::TENANT_ID,
                'code' => 'LIFT-DEMO-01',
                'name' => 'Demo Lagerlift',
                'device_type' => 'storage_lift',
                'endpoint_url' => 'https://lift.demo.webwms.local/commands',
                'credential_env' => 'DEMO_LIFT_TOKEN',
                'active' => 1,
                'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
                'changed_by' => null,
                'changed_at' => null,
            ]);
        }
        if (!$this->exists('wms_wcs_connection', self::WCS_CONNECTION_ID)) {
            $this->connection->insert('wms_wcs_connection', [
                'id' => self::WCS_CONNECTION_ID,
                'tenant_id' => self::TENANT_ID,
                'code' => 'WCS-DEMO-01',
                'name' => 'Demo Materialflussrechner',
                'system_type' => 'mfr',
                'endpoint_url' => 'https://wcs.demo.webwms.local/commands',
                'credential_env' => 'DEMO_WCS_TOKEN',
                'active' => 1,
                'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
                'changed_by' => null,
                'changed_at' => null,
            ]);
        }
        if (!$this->exists('wms_transport_endpoint', self::TRANSPORT_ENDPOINT_ID)) {
            $this->connection->insert('wms_transport_endpoint', [
                'id' => self::TRANSPORT_ENDPOINT_ID,
                'tenant_id' => self::TENANT_ID,
                'code' => 'TCP-DEMO-01',
                'name' => 'Demo Fördertechnik TCP',
                'adapter_type' => 'tcp_client',
                'address' => 'tcp://conveyor.demo.webwms.local:9100',
                'credential_env' => 'DEMO_TCP_TOKEN',
                'active' => 1,
                'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
                'changed_by' => null,
                'changed_at' => null,
            ]);
            $this->connection->insert('wms_protocol_configuration', [
                'endpoint_id' => self::TRANSPORT_ENDPOINT_ID,
                'protocol' => 'raw_tcp',
                'framing' => 'stx_etx',
                'connect_timeout_ms' => 3000,
                'read_timeout_ms' => 10000,
            ]);
        }

        $tenantId = new TenantId(self::TENANT_ID);
        if (!$this->exists('wms_warehouse', self::WAREHOUSE_ID)) {
            $this->inventory->saveWarehouse(new Warehouse(
                new InventoryId(self::WAREHOUSE_ID),
                $tenantId,
                new SiteId(self::SITE_ID),
                'DEMO-01',
                'Demo-Lager',
                $now,
            ));
        }
        $this->createLocation($tenantId, self::LOCATION_A_ID, 'A-01-01', $now);
        $this->createLocation($tenantId, self::LOCATION_B_ID, 'B-01-01', $now);
        $this->createTopologyDemo($now);
        $this->createProduct($tenantId, self::PRODUCT_A_ID, 'DEMO-1000', 'Demo Scanner', $now);
        $this->createProduct($tenantId, self::PRODUCT_B_ID, 'DEMO-2000', 'Demo Versandkarton', $now);
        $this->createPlannedInboundDemo($now);
        $this->createStock($tenantId, self::PRODUCT_A_ID, self::LOCATION_A_ID, 25, $now);
        $this->createStock($tenantId, self::PRODUCT_B_ID, self::LOCATION_B_ID, 100, $now);
        if (!$this->exists('wms_outbound_order', self::ORDER_ID)) {
            $this->inventory->saveOutboundOrder(new OutboundOrder(
                new InventoryId(self::ORDER_ID),
                $tenantId,
                'DEMO-ORDER-001',
                'DEMO-CUSTOMER-001',
                [new OutboundOrderItem(new InventoryId(self::ORDER_ITEM_ID), new InventoryId(self::PRODUCT_A_ID), 2)],
                new UserId(self::USER_ID),
                $now,
            ));
        }
        $this->createFulfillmentControlDemo($now);
        $this->extendedDataset->generate($now);

        return new DemoBootstrapResult(self::TENANT_ID, self::EMAIL, $generatedPassword, $created);
    }

    private function createTopologyDemo(DateTimeImmutable $now): void
    {
        $this->connection->update('wms_site', ['created_by' => self::USER_ID], ['id' => self::SITE_ID]);
        $this->connection->update('wms_warehouse', [
            'warehouse_type' => 'standard', 'created_by' => self::USER_ID,
        ], ['id' => self::WAREHOUSE_ID]);
        if (!$this->exists('wms_warehouse_area', self::AREA_ID)) {
            $this->connection->insert('wms_warehouse_area', [
                'id' => self::AREA_ID, 'tenant_id' => self::TENANT_ID, 'warehouse_id' => self::WAREHOUSE_ID,
                'code' => 'STORAGE', 'name' => 'Demo-Lagerbereich', 'area_type' => 'storage',
                'created_by' => self::USER_ID, 'created_at' => $this->date($now),
            ]);
        }
        if (!$this->exists('wms_warehouse_aisle', self::AISLE_ID)) {
            $this->connection->insert('wms_warehouse_aisle', [
                'id' => self::AISLE_ID, 'tenant_id' => self::TENANT_ID, 'area_id' => self::AREA_ID,
                'code' => '01', 'name' => 'Demo-Gang 01', 'created_by' => self::USER_ID,
                'created_at' => $this->date($now),
            ]);
        }
        $this->connection->update('wms_storage_location', [
            'area_id' => self::AREA_ID, 'aisle_id' => self::AISLE_ID, 'level_code' => '01', 'bin_code' => '01',
            'location_type' => 'storage', 'capacity_quantity' => 100, 'created_by' => self::USER_ID,
        ], ['id' => self::LOCATION_A_ID]);
        $this->connection->update('wms_storage_location', [
            'area_id' => self::AREA_ID, 'aisle_id' => self::AISLE_ID, 'level_code' => '01', 'bin_code' => '02',
            'location_type' => 'storage', 'capacity_quantity' => 200, 'created_by' => self::USER_ID,
        ], ['id' => self::LOCATION_B_ID]);
    }

    private function createPlannedInboundDemo(DateTimeImmutable $now): void
    {
        if (!$this->exists('wms_purchase_order', self::PURCHASE_ORDER_ID)) {
            $this->connection->insert('wms_purchase_order', [
                'id' => self::PURCHASE_ORDER_ID, 'tenant_id' => self::TENANT_ID,
                'code' => 'DEMO-PO-001', 'supplier_reference' => 'DEMO-SUPPLIER-001', 'status' => 'advised',
                'created_by' => self::USER_ID, 'created_at' => $this->date($now), 'updated_at' => $this->date($now),
            ]);
            $this->connection->insert('wms_purchase_order_item', [
                'id' => self::PURCHASE_ORDER_ITEM_ID, 'purchase_order_id' => self::PURCHASE_ORDER_ID,
                'product_id' => self::PRODUCT_A_ID, 'ordered_quantity' => 5, 'advised_quantity' => 5,
                'received_quantity' => 0, 'status' => 'advised',
            ]);
        }
        if (!$this->exists('wms_inbound_delivery', self::INBOUND_DELIVERY_ID)) {
            $this->connection->insert('wms_inbound_delivery', [
                'id' => self::INBOUND_DELIVERY_ID, 'tenant_id' => self::TENANT_ID,
                'purchase_order_id' => self::PURCHASE_ORDER_ID, 'code' => 'DEMO-IN-001',
                'delivery_note' => 'LS-DEMO-001', 'expected_at' => $this->date($now), 'status' => 'advised',
                'created_by' => self::USER_ID, 'created_at' => $this->date($now), 'updated_at' => $this->date($now),
            ]);
            $this->connection->insert('wms_inbound_delivery_line', [
                'id' => self::INBOUND_DELIVERY_LINE_ID, 'inbound_delivery_id' => self::INBOUND_DELIVERY_ID,
                'purchase_order_item_id' => self::PURCHASE_ORDER_ITEM_ID, 'advised_quantity' => 5, 'status' => 'advised',
            ]);
        }
        if (!$this->exists('wms_putaway_strategy', self::PUTAWAY_STRATEGY_ID)) {
            $this->connection->insert('wms_putaway_strategy', [
                'id' => self::PUTAWAY_STRATEGY_ID, 'tenant_id' => self::TENANT_ID,
                'warehouse_id' => self::WAREHOUSE_ID, 'code' => 'DEMO-PUTAWAY', 'stock_status' => 'available',
                'location_prefix' => 'B-', 'priority' => 10, 'enabled' => 1,
                'created_by' => self::USER_ID, 'created_at' => $this->date($now),
            ]);
        }
    }

    private function createAdministrationDemo(DateTimeImmutable $now): void
    {
        if (!$this->exists('wms_business_partner', self::BUSINESS_PARTNER_ID)) {
            $this->connection->insert('wms_business_partner', ['id' => self::BUSINESS_PARTNER_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'demo-customer', 'name' => 'Demo Kunde GmbH', 'partner_type' => 'customer', 'external_reference' => 'ERP-10001', 'active' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_tenant_context', self::TENANT_CONTEXT_ID)) {
            $this->connection->insert('wms_tenant_context', ['id' => self::TENANT_CONTEXT_ID, 'tenant_id' => self::TENANT_ID, 'business_partner_id' => self::BUSINESS_PARTNER_ID, 'code' => 'demo-customer', 'name' => 'Demo Kundendatenraum', 'active' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_number_range', self::NUMBER_RANGE_ID)) {
            $this->connection->insert('wms_number_range', ['id' => self::NUMBER_RANGE_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'outbound', 'name' => 'Warenausgang', 'object_type' => 'outbound_order', 'prefix' => 'AU-', 'suffix' => '', 'padding' => 8, 'next_value' => 10001, 'maximum_value' => 99999999, 'gs1_company_prefix' => null, 'enabled' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_device_profile', self::DEVICE_PROFILE_ID)) {
            $this->connection->insert('wms_device_profile', ['id' => self::DEVICE_PROFILE_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'mde-default', 'name' => 'Standard MDE', 'device_type' => 'scanner', 'start_route' => '/v3/inbound/planned', 'fullscreen' => 1, 'scan_suffix' => 'Enter', 'enabled' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if ($this->connection->fetchOne('SELECT 1 FROM wms_process_configuration WHERE tenant_id = :tenantId AND process_key = :processKey', ['tenantId' => self::TENANT_ID, 'processKey' => 'inbound.quality']) === false) {
            $this->connection->insert('wms_process_configuration', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => self::TENANT_ID, 'process_key' => 'inbound.quality', 'name' => 'Qualitätsprüfung im Wareneingang', 'enabled' => 1, 'configuration' => '{}', 'changed_by' => self::USER_ID, 'changed_at' => $this->date($now)]);
        }
        if ($this->connection->fetchOne('SELECT 1 FROM wms_quality_checklist WHERE tenant_id = :tenantId AND code = :code', ['tenantId' => self::TENANT_ID, 'code' => 'INBOUND-STANDARD']) === false) {
            $this->connection->insert('wms_quality_checklist', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => self::TENANT_ID, 'code' => 'INBOUND-STANDARD', 'name' => 'Standardprüfung Wareneingang', 'questions' => json_encode(['Verpackung unbeschädigt?', 'Artikelidentität korrekt?', 'Menge vollständig?'], JSON_THROW_ON_ERROR), 'active' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if ($this->connection->fetchOne('SELECT 1 FROM wms_deployment_configuration WHERE tenant_id = :tenantId', ['tenantId' => self::TENANT_ID]) === false) {
            $this->connection->insert('wms_deployment_configuration', ['tenant_id' => self::TENANT_ID, 'deployment_mode' => 'on_premises', 'public_url' => 'http://www.webwms.local', 'storage_driver' => 'local', 'queue_transport' => 'rabbitmq', 'release_channel' => 'stable', 'changed_by' => self::USER_ID, 'changed_at' => $this->date($now)]);
        }
    }

    private function createFulfillmentControlDemo(DateTimeImmutable $now): void
    {
        if (!$this->exists('wms_forklift', self::FORKLIFT_ID)) {
            $this->connection->insert('wms_forklift', ['id' => self::FORKLIFT_ID, 'tenant_id' => self::TENANT_ID, 'warehouse_id' => self::WAREHOUSE_ID, 'code' => 'forklift-01', 'name' => 'Demo Stapler 01', 'resource_type' => 'forklift', 'status' => 'available', 'assigned_user_id' => self::USER_ID, 'last_location_id' => self::LOCATION_A_ID, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_transport_rule', self::TRANSPORT_RULE_ID)) {
            $this->connection->insert('wms_transport_rule', ['id' => self::TRANSPORT_RULE_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'storage-to-pick', 'name' => 'Lager zur Zugriffszone', 'trigger_type' => 'prepositioning', 'source_prefix' => 'A-', 'target_prefix' => 'B-', 'transport_type' => 'prepositioning', 'resource_type' => 'forklift', 'priority' => 80, 'enabled' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_process_station', self::STATION_A_ID)) {
            $this->connection->insert('wms_process_station', ['id' => self::STATION_A_ID, 'tenant_id' => self::TENANT_ID, 'warehouse_id' => self::WAREHOUSE_ID, 'location_id' => self::LOCATION_A_ID, 'code' => 'station-a', 'name' => 'Lagerstation A', 'station_type' => 'storage', 'sequence_number' => 10, 'active' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_process_station', self::STATION_B_ID)) {
            $this->connection->insert('wms_process_station', ['id' => self::STATION_B_ID, 'tenant_id' => self::TENANT_ID, 'warehouse_id' => self::WAREHOUSE_ID, 'location_id' => self::LOCATION_B_ID, 'code' => 'station-b', 'name' => 'Bereitstellung B', 'station_type' => 'buffer', 'sequence_number' => 20, 'active' => 1, 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
        }
        if (!$this->exists('wms_milk_run', self::MILK_RUN_ID)) {
            $this->connection->insert('wms_milk_run', ['id' => self::MILK_RUN_ID, 'tenant_id' => self::TENANT_ID, 'code' => 'milk-run-01', 'name' => 'Demo Routenzug', 'warehouse_id' => self::WAREHOUSE_ID, 'schedule_type' => 'fixed', 'interval_minutes' => 60, 'next_departure_at' => null, 'status' => 'active', 'created_by' => self::USER_ID, 'created_at' => $this->date($now)]);
            $this->connection->insert('wms_milk_run_stop', ['id' => Uuid::v7()->toRfc4122(), 'milk_run_id' => self::MILK_RUN_ID, 'station_id' => self::STATION_A_ID, 'sequence_number' => 1, 'dwell_minutes' => 5]);
            $this->connection->insert('wms_milk_run_stop', ['id' => Uuid::v7()->toRfc4122(), 'milk_run_id' => self::MILK_RUN_ID, 'station_id' => self::STATION_B_ID, 'sequence_number' => 2, 'dwell_minutes' => 5]);
        }
    }

    private function createLocation(TenantId $tenantId, string $id, string $code, DateTimeImmutable $now): void
    {
        if (!$this->exists('wms_storage_location', $id)) {
            $this->inventory->saveLocation(new StorageLocation(
                new InventoryId($id),
                $tenantId,
                new InventoryId(self::WAREHOUSE_ID),
                $code,
                $now,
            ));
        }
    }

    private function createProduct(TenantId $tenantId, string $id, string $sku, string $name, DateTimeImmutable $now): void
    {
        if (!$this->exists('wms_product_reference', $id)) {
            $this->inventory->saveProduct(new ProductReference(new InventoryId($id), $tenantId, new Sku($sku), $name, $now));
        }
    }

    private function createStock(TenantId $tenantId, string $productId, string $locationId, int $quantity, DateTimeImmutable $now): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_stock_balance WHERE tenant_id = :tenantId AND product_id = :productId AND location_id = :locationId',
            ['tenantId' => self::TENANT_ID, 'productId' => $productId, 'locationId' => $locationId],
        ) !== false) {
            return;
        }
        $this->inventory->post(new StockPosting(
            new InventoryId(Uuid::v7()->toRfc4122()),
            $tenantId,
            new InventoryId($productId),
            new InventoryId($locationId),
            $quantity,
            'Demo-Anfangsbestand',
            new UserId(self::USER_ID),
            $now,
        ));
    }

    private function exists(string $table, string $id): bool
    {
        return $this->connection->fetchOne(sprintf('SELECT 1 FROM %s WHERE id = :id', $table), ['id' => $id]) !== false;
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }

    private function generatePassword(): string
    {
        return bin2hex(random_bytes(16));
    }
}
