<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PackingPackage;

final readonly class AddPackingPackageHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(AddPackingPackageCommand $command): void
    {
        $this->inventory->savePackingPackage(new PackingPackage(new InventoryId($command->packageId), new InventoryId($command->orderId), new TenantId($command->tenantId), $command->packageNumber, $command->weightGrams, array_map(static fn (string $id): InventoryId => new InventoryId($id), $command->pickTaskIds), new UserId($command->packedBy), $command->packedAt));
    }
}
