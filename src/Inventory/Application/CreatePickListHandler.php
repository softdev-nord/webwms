<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PickList;

final readonly class CreatePickListHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreatePickListCommand $command): void
    {
        $this->inventory->savePickList(new PickList(new InventoryId($command->pickListId), new TenantId($command->tenantId), $command->code, array_map(static fn (string $id): InventoryId => new InventoryId($id), $command->allocationIds), new UserId($command->createdBy), $command->createdAt));
    }
}
