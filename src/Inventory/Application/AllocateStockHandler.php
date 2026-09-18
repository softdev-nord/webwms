<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StockAllocation;
use WebWMS\Inventory\Domain\StockAllocationResult;
use WebWMS\Inventory\Domain\StockDimensions;

final readonly class AllocateStockHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(AllocateStockCommand $command): StockAllocationResult
    {
        return $this->inventory->allocate(new StockAllocation(
            new InventoryId($command->allocationId),
            new InventoryId($command->reservationId),
            new TenantId($command->tenantId),
            new InventoryId($command->productId),
            new InventoryId($command->locationId),
            StockDimensions::fromInput(
                $command->status,
                $command->batchNumber,
                $command->serialNumber,
                $command->expiresAt,
            ),
            $command->quantity,
            new UserId($command->createdBy),
            $command->createdAt,
        ));
    }
}
