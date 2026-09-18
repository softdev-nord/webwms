<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\StockPosting;

final readonly class PostStockHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(PostStockCommand $command): int
    {
        return $this->inventory->post(new StockPosting(
            new InventoryId($command->postingId),
            new TenantId($command->tenantId),
            new InventoryId($command->productId),
            new InventoryId($command->locationId),
            $command->quantityDelta,
            $command->reason,
            new UserId($command->performedBy),
            $command->occurredAt,
            StockDimensions::fromInput(
                $command->status,
                $command->batchNumber,
                $command->serialNumber,
                $command->expiresAt,
            ),
        ));
    }
}
