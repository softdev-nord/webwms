<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\AllocationTransitionType;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StockAllocationTransition;
use WebWMS\Inventory\Domain\StockFulfillmentResult;

final readonly class ConsumeStockAllocationHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(ConsumeStockAllocationCommand $command): StockFulfillmentResult
    {
        return $this->inventory->transitionAllocation(new StockAllocationTransition(
            new InventoryId($command->allocationId),
            new TenantId($command->tenantId),
            AllocationTransitionType::Consume,
            new InventoryId($command->ledgerEntryId),
            $command->reason,
            new UserId($command->performedBy),
            $command->occurredAt,
        ));
    }
}
