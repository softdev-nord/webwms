<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\LoadingManifest;

final readonly class CreateLoadingManifestHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateLoadingManifestCommand $command): void
    {
        $shipmentIds = array_map(static fn (string $id): InventoryId => new InventoryId($id), $command->shipmentIds);
        $this->inventory->saveLoadingManifest(new LoadingManifest(new InventoryId($command->manifestId), new TenantId($command->tenantId), $command->code, $command->tourReference, $command->vehicleReference, $shipmentIds, new UserId($command->createdBy), $command->createdAt));
    }
}
