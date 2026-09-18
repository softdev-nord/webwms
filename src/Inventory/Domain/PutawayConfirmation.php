<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PutawayConfirmation
{
    public function __construct(
        private InventoryId $orderId,
        private InventoryId $transferId,
        private InventoryId $sourceLedgerId,
        private InventoryId $destinationLedgerId,
        private TenantId $tenantId,
        private UserId $confirmedBy,
        private DateTimeImmutable $confirmedAt
    ) {
        $ids = [$transferId->value(), $sourceLedgerId->value(), $destinationLedgerId->value()];
        if (count(array_unique($ids)) !== 3) {
            throw new InvalidArgumentException('Putaway transfer and ledger IDs must be different.');
        }
    }

    public function orderId(): InventoryId
    {
        return $this->orderId;
    }

    public function transferId(): InventoryId
    {
        return $this->transferId;
    }

    public function sourceLedgerId(): InventoryId
    {
        return $this->sourceLedgerId;
    }

    public function destinationLedgerId(): InventoryId
    {
        return $this->destinationLedgerId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function confirmedBy(): UserId
    {
        return $this->confirmedBy;
    }

    public function confirmedAt(): DateTimeImmutable
    {
        return $this->confirmedAt;
    }
}
