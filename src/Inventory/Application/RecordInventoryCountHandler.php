<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryCountEntry;
use WebWMS\Inventory\Domain\InventoryCountResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class RecordInventoryCountHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(RecordInventoryCountCommand $command): InventoryCountResult
    {
        return $this->inventory->recordInventoryCount(new InventoryCountEntry(new InventoryId($command->countId), new InventoryId($command->lineId), new TenantId($command->tenantId), $command->countedQuantity, new UserId($command->countedBy), $command->countedAt));
    }
}
