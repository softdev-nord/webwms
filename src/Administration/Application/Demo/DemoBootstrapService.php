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

    public const string PRODUCT_A_ID = 'af7b6174-a744-4346-a243-6f5e59e1de4f';

    public const string PRODUCT_B_ID = 'dcfbe1d5-7210-4f28-afc7-e098214f102f';

    public const string ORDER_ID = '3b190f7e-aa8d-412f-bf6e-a102d99d2b72';

    public const string ORDER_ITEM_ID = '5f55fe9e-2396-4861-8dc8-a3b6088d53fc';

    public const string PRINTER_ID = 'd1eac343-b344-4ea1-9f68-6e6303e0f3f9';

    public const string DEVICE_ID = 'e2a71554-20cb-4a79-a5cc-f65215d2ca21';

    public const string MEASUREMENT_DEVICE_ID = 'f2d82665-29dc-4d98-8c91-c2e82504f1d4';

    public const string AUTOMATION_DEVICE_ID = 'a67562f8-9eb5-4b7f-a93b-39132c7d0e11';

    public const string EMAIL = 'admin@demo.webwms.local';

    public function __construct(
        private Connection $connection,
        private PasswordHasher $passwordHasher,
        private InventoryRepository $inventory,
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
        $this->createProduct($tenantId, self::PRODUCT_A_ID, 'DEMO-1000', 'Demo Scanner', $now);
        $this->createProduct($tenantId, self::PRODUCT_B_ID, 'DEMO-2000', 'Demo Versandkarton', $now);
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

        return new DemoBootstrapResult(self::TENANT_ID, self::EMAIL, $generatedPassword, $created);
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
