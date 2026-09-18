<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ReplenishmentConfirmation;
use WebWMS\Inventory\Domain\ReplenishmentResult;

final readonly class ConfirmReplenishmentHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(ConfirmReplenishmentCommand $command): ReplenishmentResult
    {
        return $this->inventory->confirmReplenishment(new ReplenishmentConfirmation(new InventoryId($command->orderId), new InventoryId($command->transferId), new InventoryId($command->sourceLedgerId), new InventoryId($command->destinationLedgerId), new TenantId($command->tenantId), new UserId($command->confirmedBy), $command->confirmedAt));
    }
}
