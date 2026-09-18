<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\CycleCountPlan;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class CreateCycleCountPlanHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateCycleCountPlanCommand $command): void
    {
        $this->inventory->saveCycleCountPlan(new CycleCountPlan(
            new InventoryId($command->planId),
            new TenantId($command->tenantId),
            new InventoryId($command->warehouseId),
            $command->code,
            $command->locationPrefix,
            $command->intervalDays,
            $command->nextDueAt,
            new UserId($command->createdBy),
            $command->createdAt,
        ));
    }
}
