<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

interface InventoryRepository
{
    public function saveProduct(ProductReference $product): void;
    public function saveWarehouse(Warehouse $warehouse): void;
    public function saveLocation(StorageLocation $location): void;
    public function post(StockPosting $posting): int;
    public function transfer(StockTransfer $transfer): StockTransferResult;
}
