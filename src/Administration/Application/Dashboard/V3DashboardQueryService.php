<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Dashboard;

use Doctrine\DBAL\Connection;

final readonly class V3DashboardQueryService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /** @return array{tenant: string, products: int, warehouses: int, locations: int, stock: int, outboundOrders: int, pickLists: int, packingOrders: int, shipments: int} */
    public function summary(string $tenantId): array
    {
        return [
            'tenant' => (string) $this->connection->fetchOne('SELECT name FROM wms_tenant WHERE id = :tenantId', ['tenantId' => $tenantId]),
            'products' => $this->count('wms_product_reference', $tenantId),
            'warehouses' => $this->count('wms_warehouse', $tenantId),
            'locations' => $this->count('wms_storage_location', $tenantId),
            'stock' => (int) $this->connection->fetchOne('SELECT COALESCE(SUM(quantity), 0) FROM wms_stock_balance WHERE tenant_id = :tenantId', ['tenantId' => $tenantId]),
            'outboundOrders' => $this->count('wms_outbound_order', $tenantId),
            'pickLists' => $this->count('wms_pick_list', $tenantId),
            'packingOrders' => $this->count('wms_packing_order', $tenantId),
            'shipments' => $this->count('wms_shipment', $tenantId),
        ];
    }

    private function count(string $table, string $tenantId): int
    {
        return (int) $this->connection->fetchOne(
            sprintf('SELECT COUNT(*) FROM %s WHERE tenant_id = :tenantId', $table),
            ['tenantId' => $tenantId],
        );
    }
}
