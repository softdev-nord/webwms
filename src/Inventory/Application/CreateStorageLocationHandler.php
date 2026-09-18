<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StorageLocation;

final readonly class CreateStorageLocationHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateStorageLocationCommand $command): StorageLocation
    {
        $location = new StorageLocation(
            new InventoryId($command->locationId),
            new TenantId($command->tenantId),
            new InventoryId($command->warehouseId),
            strtoupper(trim($command->code)),
            $command->occurredAt,
        );
        $this->inventory->saveLocation($location);

        return $location;
    }
}
