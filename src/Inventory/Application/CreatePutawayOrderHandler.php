<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PutawayRequest;
use WebWMS\Inventory\Domain\PutawayResult;

final readonly class CreatePutawayOrderHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreatePutawayOrderCommand $command): PutawayResult
    {
        return $this->inventory->createPutawayOrder(new PutawayRequest(new InventoryId($command->orderId), new TenantId($command->tenantId), new InventoryId($command->inboundReceiptId), new UserId($command->createdBy), $command->createdAt));
    }
}
