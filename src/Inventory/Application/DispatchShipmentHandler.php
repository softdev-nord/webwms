<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ShipmentDispatch;
use WebWMS\Inventory\Domain\ShipmentResult;

final readonly class DispatchShipmentHandler
{
    public function __construct(private InventoryRepository $inventory) {}

    public function __invoke(DispatchShipmentCommand $command): ShipmentResult
    {
        return $this->inventory->dispatchShipment(new ShipmentDispatch(new InventoryId($command->shipmentId), new TenantId($command->tenantId), $command->handoverReference, new UserId($command->dispatchedBy), $command->dispatchedAt));
    }
}
