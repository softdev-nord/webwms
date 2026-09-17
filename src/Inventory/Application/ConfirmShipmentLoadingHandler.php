<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\LoadingResult;
use WebWMS\Inventory\Domain\ShipmentLoading;

final readonly class ConfirmShipmentLoadingHandler
{
    public function __construct(private InventoryRepository $inventory) {}

    public function __invoke(ConfirmShipmentLoadingCommand $command): LoadingResult
    {
        return $this->inventory->confirmShipmentLoading(new ShipmentLoading(new InventoryId($command->manifestId), new TenantId($command->tenantId), new InventoryId($command->shipmentId), new UserId($command->loadedBy), $command->loadedAt));
    }
}
