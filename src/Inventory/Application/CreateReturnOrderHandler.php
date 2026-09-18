<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ReturnItem;
use WebWMS\Inventory\Domain\ReturnOrder;

final readonly class CreateReturnOrderHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateReturnOrderCommand $command): void
    {
        $items = array_map(static fn (array $item): ReturnItem => new ReturnItem(new InventoryId($item['id']), new InventoryId($item['productId']), $item['quantity'], $item['reason']), $command->items);
        $this->inventory->saveReturnOrder(new ReturnOrder(new InventoryId($command->returnOrderId), new TenantId($command->tenantId), $command->code, $command->orderReference, $items, new UserId($command->createdBy), $command->createdAt));
    }
}
