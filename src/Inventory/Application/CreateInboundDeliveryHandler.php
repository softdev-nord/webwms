<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundDelivery;
use WebWMS\Inventory\Domain\InboundDeliveryLine;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;

final readonly class CreateInboundDeliveryHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(CreateInboundDeliveryCommand $command): void
    {
        $lines = array_map(static fn (array $line): InboundDeliveryLine => new InboundDeliveryLine(new InventoryId($line['id']), new InventoryId($line['purchaseOrderItemId']), $line['quantity']), $command->lines);
        $this->inventory->saveInboundDelivery(new InboundDelivery(new InventoryId($command->deliveryId), new TenantId($command->tenantId), new InventoryId($command->purchaseOrderId), $command->code, $command->deliveryNote, $command->expectedAt, $lines, new UserId($command->createdBy), $command->createdAt));
    }
}
