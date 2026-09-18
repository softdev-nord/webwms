<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryCountPlan;
use WebWMS\Inventory\Domain\InventoryCountResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class CreateInventoryCountHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CreateInventoryCountCommand $command): InventoryCountResult
    {
        return $this->inventory->createInventoryCount(new InventoryCountPlan(new InventoryId($command->countId), new TenantId($command->tenantId), new InventoryId($command->warehouseId), $command->code, $command->locationPrefix, new UserId($command->createdBy), $command->createdAt));
    }
}
