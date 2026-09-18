<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ReturnReceipt;
use WebWMS\Inventory\Domain\ReturnResult;

final readonly class ReceiveReturnHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(ReceiveReturnCommand $command): ReturnResult
    {
        return $this->inventory->receiveReturn(new ReturnReceipt(new InventoryId($command->receiptId), new TenantId($command->tenantId), new InventoryId($command->returnOrderId), new InventoryId($command->returnItemId), new UserId($command->receivedBy), $command->receivedAt));
    }
}
