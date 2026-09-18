<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StockReservation;

final readonly class CreateStockReservationHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateStockReservationCommand $command): void
    {
        $this->inventory->saveReservation(new StockReservation(
            new InventoryId($command->reservationId),
            new TenantId($command->tenantId),
            new InventoryId($command->productId),
            $command->orderReference,
            $command->quantity,
            new UserId($command->createdBy),
            $command->createdAt,
        ));
    }
}
