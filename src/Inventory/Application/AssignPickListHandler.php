<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PickListAssignment;

final readonly class AssignPickListHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(AssignPickListCommand $command): void
    {
        $this->inventory->assignPickList(new PickListAssignment(new InventoryId($command->pickListId), new TenantId($command->tenantId), new UserId($command->assignedTo), new UserId($command->assignedBy), $command->assignedAt));
    }
}
