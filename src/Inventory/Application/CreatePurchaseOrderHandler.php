<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PurchaseOrder;
use WebWMS\Inventory\Domain\PurchaseOrderItem;

final readonly class CreatePurchaseOrderHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CreatePurchaseOrderCommand $command): void
    {
        $items = array_map(static fn (array $item): PurchaseOrderItem => new PurchaseOrderItem(new InventoryId($item['id']), new InventoryId($item['productId']), $item['quantity']), $command->items);
        $this->inventory->savePurchaseOrder(new PurchaseOrder(new InventoryId($command->purchaseOrderId), new TenantId($command->tenantId), $command->code, $command->supplierReference, $items, new UserId($command->createdBy), $command->createdAt));
    }
}
