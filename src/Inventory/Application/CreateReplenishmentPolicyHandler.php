<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ReplenishmentPolicy;

final readonly class CreateReplenishmentPolicyHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CreateReplenishmentPolicyCommand $command): void
    {
        $this->inventory->saveReplenishmentPolicy(new ReplenishmentPolicy(new InventoryId($command->policyId), new TenantId($command->tenantId), new InventoryId($command->warehouseId), new InventoryId($command->productId), new InventoryId($command->targetLocationId), $command->code, $command->sourceLocationPrefix, $command->minimumQuantity, $command->targetQuantity, $command->priority, new UserId($command->createdBy), $command->createdAt));
    }
}
