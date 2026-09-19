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
    public const TENANT_ID = '11111111-1111-4111-8111-111111111111';
    public const SITE_ID = '22222222-2222-4222-8222-222222222222';
    public const ROLE_ID = '33333333-3333-4333-8333-333333333333';
    public const USER_ID = '44444444-4444-4444-8444-444444444444';
    public const WAREHOUSE_ID = '55555555-5555-4555-8555-555555555555';
    public const LOCATION_A_ID = '66666666-6666-4666-8666-666666666661';
    public const LOCATION_B_ID = '66666666-6666-4666-8666-666666666662';
    public const PRODUCT_A_ID = '77777777-7777-4777-8777-777777777771';
    public const PRODUCT_B_ID = '77777777-7777-4777-8777-777777777772';
    public const ORDER_ID = '88888888-8888-4888-8888-888888888888';
    public const ORDER_ITEM_ID = '99999999-9999-4999-8999-999999999999';
    public const PRINTER_ID = 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa';
    public const EMAIL = 'admin@demo.webwms.local';

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
                'id' => self::TENANT_ID, 'name' => 'WebWMS Demo', 'status' => 'active',
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
        return rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
    }
}
