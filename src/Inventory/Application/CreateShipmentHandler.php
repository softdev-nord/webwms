<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\Shipment;

final readonly class CreateShipmentHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateShipmentCommand $command): void
    {
        $this->inventory->saveShipment(new Shipment(new InventoryId($command->shipmentId), new TenantId($command->tenantId), new InventoryId($command->packingOrderId), $command->shipmentNumber, $command->carrier, $command->service, new UserId($command->createdBy), $command->createdAt));
    }
}
