<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PutawayStrategy;
use WebWMS\Inventory\Domain\StockStatus;

final readonly class CreatePutawayStrategyHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CreatePutawayStrategyCommand $command): void
    {
        $status = StockStatus::tryFrom(mb_strtolower(trim($command->stockStatus))) ?? throw new InvalidArgumentException('The putaway stock status is not supported.');
        $this->inventory->savePutawayStrategy(new PutawayStrategy(new InventoryId($command->strategyId), new TenantId($command->tenantId), new InventoryId($command->warehouseId), $command->code, $status, $command->locationPrefix, $command->priority, new UserId($command->createdBy), $command->createdAt));
    }
}
