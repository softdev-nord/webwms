<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\CycleCountExecution;
use WebWMS\Inventory\Domain\InventoryCountResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class StartDueCycleCountHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(StartDueCycleCountCommand $command): InventoryCountResult
    {
        return $this->inventory->createDueCycleCount(new CycleCountExecution(
            new InventoryId($command->planId),
            new InventoryId($command->countId),
            new TenantId($command->tenantId),
            $command->countCode,
            new UserId($command->startedBy),
            $command->startedAt,
        ));
    }
}
