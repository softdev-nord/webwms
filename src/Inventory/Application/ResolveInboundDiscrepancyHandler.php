<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundDiscrepancyResolution;
use WebWMS\Inventory\Domain\InboundResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class ResolveInboundDiscrepancyHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(ResolveInboundDiscrepancyCommand $command): InboundResult
    {
        return $this->inventory->resolveInboundDiscrepancy(new InboundDiscrepancyResolution(
            new InventoryId($command->receiptId),
            new TenantId($command->tenantId),
            $command->action,
            $command->note,
            new InventoryId($command->transferId),
            new InventoryId($command->sourceLedgerId),
            new InventoryId($command->destinationLedgerId),
            new UserId($command->resolvedBy),
            $command->resolvedAt,
        ));
    }
}
