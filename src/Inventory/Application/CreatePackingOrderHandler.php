<?php
declare(strict_types=1);
namespace WebWMS\Inventory\Application;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PackingOrder;
final readonly class CreatePackingOrderHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CreatePackingOrderCommand $command): void
    {
        $this->inventory->savePackingOrder(new PackingOrder(new InventoryId($command->orderId), new TenantId($command->tenantId), new InventoryId($command->pickListId), $command->code, new UserId($command->createdBy), $command->createdAt));
    }
}
