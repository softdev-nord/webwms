<?php
declare(strict_types=1);
namespace WebWMS\Inventory\Application;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PackingCompletion;
use WebWMS\Inventory\Domain\PackingResult;
final readonly class CompletePackingOrderHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CompletePackingOrderCommand $command): PackingResult
    {
        return $this->inventory->completePackingOrder(new PackingCompletion(new InventoryId($command->orderId), new TenantId($command->tenantId), new UserId($command->completedBy), $command->completedAt));
    }
}
