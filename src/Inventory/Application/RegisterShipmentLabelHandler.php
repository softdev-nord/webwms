<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ShipmentLabel;
use WebWMS\Inventory\Domain\ShipmentResult;

final readonly class RegisterShipmentLabelHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(RegisterShipmentLabelCommand $command): ShipmentResult
    {
        return $this->inventory->registerShipmentLabel(new ShipmentLabel(new InventoryId($command->shipmentId), new TenantId($command->tenantId), $command->trackingNumber, $command->labelReference, new UserId($command->registeredBy), $command->registeredAt));
    }
}
