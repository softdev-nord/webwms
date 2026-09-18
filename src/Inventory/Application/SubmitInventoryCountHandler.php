<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryCountResult;
use WebWMS\Inventory\Domain\InventoryCountSubmission;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class SubmitInventoryCountHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(SubmitInventoryCountCommand $command): InventoryCountResult
    {
        return $this->inventory->submitInventoryCount(new InventoryCountSubmission(new InventoryId($command->countId), new TenantId($command->tenantId), new UserId($command->submittedBy), $command->submittedAt));
    }
}
