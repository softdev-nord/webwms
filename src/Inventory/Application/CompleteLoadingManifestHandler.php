<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\LoadingCompletion;
use WebWMS\Inventory\Domain\LoadingResult;

final readonly class CompleteLoadingManifestHandler
{
    public function __construct(private InventoryRepository $inventory) {}

    public function __invoke(CompleteLoadingManifestCommand $command): LoadingResult
    {
        return $this->inventory->completeLoadingManifest(new LoadingCompletion(new InventoryId($command->manifestId), new TenantId($command->tenantId), new UserId($command->completedBy), $command->completedAt));
    }
}
