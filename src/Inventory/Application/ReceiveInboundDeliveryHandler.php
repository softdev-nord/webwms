<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundReceipt;
use WebWMS\Inventory\Domain\InboundResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class ReceiveInboundDeliveryHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(ReceiveInboundDeliveryCommand $command): InboundResult
    {
        return $this->inventory->receiveInboundDelivery(new InboundReceipt(new InventoryId($command->receiptId), new TenantId($command->tenantId), new InventoryId($command->deliveryId), new InventoryId($command->deliveryLineId), new UserId($command->receivedBy), $command->receivedAt));
    }
}
