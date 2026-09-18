<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class OutboundOrderRelease
{
    /** @param array<string, InventoryId> $reservationIdsByItem */
    public function __construct(
        private InventoryId $orderId,
        private TenantId $tenantId,
        private array $reservationIdsByItem,
        private UserId $releasedBy,
        private DateTimeImmutable $releasedAt
    ) {
        if ($reservationIdsByItem === []) {
            throw new InvalidArgumentException('An order release requires reservation IDs.');
        }
        $ids = array_map(static fn (InventoryId $id): string => $id->value(), $reservationIdsByItem);
        if (count(array_unique($ids)) !== count($ids)) {
            throw new InvalidArgumentException('Reservation IDs must be unique.');
        }
    }

    public function orderId(): InventoryId
    {
        return $this->orderId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    /** @return array<string, InventoryId> */
    public function reservationIdsByItem(): array
    {
        return $this->reservationIdsByItem;
    }

    public function releasedBy(): UserId
    {
        return $this->releasedBy;
    }

    public function releasedAt(): DateTimeImmutable
    {
        return $this->releasedAt;
    }
}
