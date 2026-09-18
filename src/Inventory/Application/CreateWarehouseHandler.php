<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Site\SiteId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\Warehouse;

final readonly class CreateWarehouseHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(CreateWarehouseCommand $command): Warehouse
    {
        $warehouse = new Warehouse(
            new InventoryId($command->warehouseId),
            new TenantId($command->tenantId),
            new SiteId($command->siteId),
            strtoupper(trim($command->code)),
            $command->name,
            $command->occurredAt,
        );
        $this->inventory->saveWarehouse($warehouse);

        return $warehouse;
    }
}
