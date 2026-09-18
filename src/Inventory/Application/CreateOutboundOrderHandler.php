<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\OutboundOrder;
use WebWMS\Inventory\Domain\OutboundOrderItem;
use WebWMS\Inventory\Domain\OutboundOrderResult;

final readonly class CreateOutboundOrderHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateOutboundOrderCommand $command): OutboundOrderResult
    {
        $items = array_map(
            static fn (array $item): OutboundOrderItem => new OutboundOrderItem(
                new InventoryId($item['id']),
                new InventoryId($item['productId']),
                $item['quantity'],
            ),
            $command->items,
        );

        return $this->inventory->saveOutboundOrder(new OutboundOrder(
            new InventoryId($command->orderId),
            new TenantId($command->tenantId),
            $command->orderNumber,
            $command->customerReference,
            $items,
            new UserId($command->createdBy),
            $command->createdAt,
        ));
    }
}
