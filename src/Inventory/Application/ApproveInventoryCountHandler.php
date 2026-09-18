<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryCountApproval;
use WebWMS\Inventory\Domain\InventoryCountResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class ApproveInventoryCountHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(ApproveInventoryCountCommand $command): InventoryCountResult
    {
        return $this->inventory->approveInventoryCount(new InventoryCountApproval(new InventoryId($command->countId), new TenantId($command->tenantId), array_map(static fn (string $id): InventoryId => new InventoryId($id), $command->ledgerEntryIds), new UserId($command->approvedBy), $command->approvedAt));
    }
}
