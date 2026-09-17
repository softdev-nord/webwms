<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use WebWMS\Inventory\Domain\InsufficientStockException;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\InvalidSerialStockException;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\StockPosting;
use WebWMS\Inventory\Domain\StorageLocation;
use WebWMS\Inventory\Domain\Warehouse;

final readonly class DbalInventoryRepository implements InventoryRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function saveProduct(ProductReference $product): void
    {
        $this->connection->insert('wms_product_reference', [
            'id' => $product->id()->value(),
            'tenant_id' => $product->tenantId()->value(),
            'sku' => $product->sku()->value(),
            'name' => $product->name(),
            'created_at' => $product->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function saveWarehouse(Warehouse $warehouse): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_site WHERE id = :siteId AND tenant_id = :tenantId',
            ['siteId' => $warehouse->siteId()->value(), 'tenantId' => $warehouse->tenantId()->value()],
        ) === false) {
            throw new InventoryReferenceNotFoundException('The site must exist in the warehouse tenant.');
        }

        $this->connection->insert('wms_warehouse', [
            'id' => $warehouse->id()->value(),
            'tenant_id' => $warehouse->tenantId()->value(),
            'site_id' => $warehouse->siteId()->value(),
            'code' => $warehouse->code(),
            'name' => $warehouse->name(),
            'created_at' => $warehouse->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function saveLocation(StorageLocation $location): void
    {
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_warehouse WHERE id = :warehouseId AND tenant_id = :tenantId',
            [
                'warehouseId' => $location->warehouseId()->value(),
                'tenantId' => $location->tenantId()->value(),
            ],
        ) === false) {
            throw new InventoryReferenceNotFoundException('The warehouse must exist in the location tenant.');
        }

        $this->connection->insert('wms_storage_location', [
            'id' => $location->id()->value(),
            'tenant_id' => $location->tenantId()->value(),
            'warehouse_id' => $location->warehouseId()->value(),
            'code' => $location->code(),
            'created_at' => $location->createdAt()->format('Y-m-d H:i:s.u'),
        ]);
    }

    public function post(StockPosting $posting): int
    {
        return $this->connection->transactional(function (Connection $connection) use ($posting): int {
            $this->assertReferencesExist($connection, $posting);
            $current = $connection->fetchOne(
                'SELECT quantity FROM wms_stock_balance '
                . 'WHERE tenant_id = :tenantId AND product_id = :productId '
                . 'AND location_id = :locationId AND stock_key = :stockKey '
                . 'FOR UPDATE',
                $this->postingKey($posting),
            );
            $currentQuantity = $current === false ? 0 : (int) $current;
            $newQuantity = $currentQuantity + $posting->quantityDelta();

            if ($newQuantity < 0) {
                throw new InsufficientStockException('The stock posting would create negative stock.');
            }

            if ($posting->dimensions()->serialNumber() !== null && $newQuantity > 1) {
                throw new InvalidSerialStockException('A serial number can only have a stock quantity of zero or one.');
            }

            if ($current === false) {
                $connection->insert('wms_stock_balance', [
                    ...$this->balanceKey($posting),
                    ...$this->dimensionValues($posting),
                    'quantity' => $newQuantity,
                    'updated_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
                ]);
            } else {
                $connection->update(
                    'wms_stock_balance',
                    [
                        'quantity' => $newQuantity,
                        'updated_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
                    ],
                    $this->balanceKey($posting),
                );
            }

            $connection->insert('wms_stock_ledger', [
                'id' => $posting->id()->value(),
                ...$this->balanceKey($posting),
                ...$this->dimensionValues($posting),
                'quantity_delta' => $posting->quantityDelta(),
                'resulting_quantity' => $newQuantity,
                'reason' => $posting->reason(),
                'performed_by' => $posting->performedBy()->value(),
                'occurred_at' => $posting->occurredAt()->format('Y-m-d H:i:s.u'),
            ]);

            return $newQuantity;
        });
    }

    /** @return array{tenantId: string, productId: string, locationId: string, stockKey: string} */
    private function postingKey(StockPosting $posting): array
    {
        return [
            'tenantId' => $posting->tenantId()->value(),
            'productId' => $posting->productId()->value(),
            'locationId' => $posting->locationId()->value(),
            'stockKey' => $posting->dimensions()->key(),
        ];
    }

    /** @return array{tenant_id: string, product_id: string, location_id: string, stock_key: string} */
    private function balanceKey(StockPosting $posting): array
    {
        return [
            'tenant_id' => $posting->tenantId()->value(),
            'product_id' => $posting->productId()->value(),
            'location_id' => $posting->locationId()->value(),
            'stock_key' => $posting->dimensions()->key(),
        ];
    }

    /** @return array{stock_status: string, batch_number: ?string, serial_number: ?string, expires_at: ?string} */
    private function dimensionValues(StockPosting $posting): array
    {
        $dimensions = $posting->dimensions();

        return [
            'stock_status' => $dimensions->status()->value,
            'batch_number' => $dimensions->batchNumber(),
            'serial_number' => $dimensions->serialNumber(),
            'expires_at' => $dimensions->expiresAt()?->format('Y-m-d'),
        ];
    }

    private function assertReferencesExist(Connection $connection, StockPosting $posting): void
    {
        $result = $connection->fetchOne(
            'SELECT 1 FROM wms_product_reference p, wms_storage_location l, wms_user_account u '
            . 'WHERE p.id = :productId AND p.tenant_id = :tenantId '
            . 'AND l.id = :locationId AND l.tenant_id = :tenantId '
            . 'AND u.id = :performedBy AND u.tenant_id = :tenantId',
            [
                'tenantId' => $posting->tenantId()->value(),
                'productId' => $posting->productId()->value(),
                'locationId' => $posting->locationId()->value(),
                'performedBy' => $posting->performedBy()->value(),
            ],
        );

        if ($result === false) {
            throw new InventoryReferenceNotFoundException(
                'Product and storage location must exist in the posting tenant.',
            );
        }
    }
}
