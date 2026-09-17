<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\StockTransfer;
use WebWMS\Inventory\Domain\StockTransferResult;

final readonly class TransferStockHandler
{
    public function __construct(private InventoryRepository $inventory)
    {
    }

    public function __invoke(TransferStockCommand $command): StockTransferResult
    {
        return $this->inventory->transfer(new StockTransfer(
            new InventoryId($command->transferId),
            new InventoryId($command->sourcePostingId),
            new InventoryId($command->destinationPostingId),
            new TenantId($command->tenantId),
            new InventoryId($command->productId),
            new InventoryId($command->sourceLocationId),
            StockDimensions::fromInput(
                $command->sourceStatus,
                $command->batchNumber,
                $command->serialNumber,
                $command->expiresAt,
            ),
            new InventoryId($command->destinationLocationId),
            StockDimensions::fromInput(
                $command->destinationStatus,
                $command->batchNumber,
                $command->serialNumber,
                $command->expiresAt,
            ),
            $command->quantity,
            $command->reason,
            new UserId($command->performedBy),
            $command->occurredAt,
        ));
    }
}
