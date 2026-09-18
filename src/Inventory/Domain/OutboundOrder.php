<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class OutboundOrder
{
    /** @param list<OutboundOrderItem> $items */
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private string $orderNumber,
        private string $customerReference,
        private array $items,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        if (trim($orderNumber) === '' || mb_strlen($orderNumber) > 100) {
            throw new InvalidArgumentException('An outbound order number must contain 1 to 100 characters.');
        }
        if (trim($customerReference) === '' || mb_strlen($customerReference) > 100) {
            throw new InvalidArgumentException('A customer reference must contain 1 to 100 characters.');
        }
        if ($items === []) {
            throw new InvalidArgumentException('An outbound order requires at least one item.');
        }
        if (count(array_unique(array_map(
            static fn (OutboundOrderItem $item): string => $item->id()->value(),
            $items,
        ))) !== count($items)) {
            throw new InvalidArgumentException('Outbound order item IDs must be unique.');
        }
        if (count(array_unique(array_map(
            static fn (OutboundOrderItem $item): string => $item->productId()->value(),
            $items,
        ))) !== count($items)) {
            throw new InvalidArgumentException('A product may occur only once in an outbound order.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function orderNumber(): string
    {
        return trim($this->orderNumber);
    }

    public function customerReference(): string
    {
        return trim($this->customerReference);
    }

    /** @return list<OutboundOrderItem> */
    public function items(): array
    {
        return $this->items;
    }

    public function createdBy(): UserId
    {
        return $this->createdBy;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
