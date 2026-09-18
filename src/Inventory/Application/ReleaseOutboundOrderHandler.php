<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\OutboundOrderRelease;
use WebWMS\Inventory\Domain\OutboundOrderResult;

final readonly class ReleaseOutboundOrderHandler
{
    public function __construct(
        private InventoryRepository $inventory
    ) {
    }

    public function __invoke(ReleaseOutboundOrderCommand $command): OutboundOrderResult
    {
        $reservationIds = [];
        foreach ($command->reservationIdsByItem as $itemId => $reservationId) {
            $reservationIds[$itemId] = new InventoryId($reservationId);
        }

        return $this->inventory->releaseOutboundOrder(new OutboundOrderRelease(
            new InventoryId($command->orderId),
            new TenantId($command->tenantId),
            $reservationIds,
            new UserId($command->releasedBy),
            $command->releasedAt,
        ));
    }
}
