<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ReturnInspection;
use WebWMS\Inventory\Domain\ReturnQualityDecision;
use WebWMS\Inventory\Domain\ReturnResult;
use WebWMS\Inventory\Domain\StockDimensions;

final readonly class InspectReturnHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(InspectReturnCommand $command): ReturnResult
    {
        $decision = ReturnQualityDecision::tryFrom(mb_strtolower(trim($command->decision))) ?? throw new InvalidArgumentException('The return quality decision is not supported.');

        return $this->inventory->inspectReturn(new ReturnInspection(new InventoryId($command->receiptId), new InventoryId($command->ledgerEntryId), new TenantId($command->tenantId), new InventoryId($command->locationId), $decision, $command->note, new StockDimensions(batchNumber: $command->batchNumber, serialNumber: $command->serialNumber, expiresAt: $command->expiresAt), new UserId($command->inspectedBy), $command->inspectedAt));
    }
}
