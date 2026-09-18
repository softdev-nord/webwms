<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\PickConfirmation;
use WebWMS\Inventory\Domain\PickConfirmationResult;
use WebWMS\Inventory\Domain\PickOutcome;

final readonly class ConfirmPickTaskHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(ConfirmPickTaskCommand $command): PickConfirmationResult
    {
        $outcome = PickOutcome::tryFrom($command->outcome);
        if ($outcome === null) {
            throw new \InvalidArgumentException('A pick outcome must be either "picked" or "shortage".');
        }

        return $this->inventory->confirmPick(new PickConfirmation(new InventoryId($command->taskId), new TenantId($command->tenantId), $outcome, $command->ledgerEntryId === null ? null : new InventoryId($command->ledgerEntryId), $command->note, new UserId($command->confirmedBy), $command->confirmedAt));
    }
}
