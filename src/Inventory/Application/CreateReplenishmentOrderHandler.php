<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ReplenishmentRequest;
use WebWMS\Inventory\Domain\ReplenishmentResult;

final readonly class CreateReplenishmentOrderHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateReplenishmentOrderCommand $command): ReplenishmentResult
    {
        return $this->inventory->createReplenishmentOrder(new ReplenishmentRequest(new InventoryId($command->orderId), new TenantId($command->tenantId), new InventoryId($command->policyId), new UserId($command->createdBy), $command->createdAt));
    }
}
