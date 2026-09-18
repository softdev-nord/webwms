<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ReturnOrder
{
    /** @param list<ReturnItem> $items */
    public function __construct(private InventoryId $id, private TenantId $tenantId, private string $code, private string $orderReference, private array $items, private UserId $createdBy, private DateTimeImmutable $createdAt)
    {
        foreach ([$code, $orderReference] as $value) {
            if (trim($value) === '' || mb_strlen($value) > 80) {
                throw new InvalidArgumentException('Return code and order reference must contain 1 to 80 characters.');
            }
        }
        if ($items === []) {
            throw new InvalidArgumentException('A return order requires at least one item.');
        }
        $ids = array_map(static fn (ReturnItem $item): string => $item->id()->value(), $items);
        if (count(array_unique($ids)) !== count($ids)) {
            throw new InvalidArgumentException('A return item may only occur once.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function code(): string { return mb_strtoupper(trim($this->code)); }
    public function orderReference(): string { return trim($this->orderReference); }
    /** @return list<ReturnItem> */
    public function items(): array { return $this->items; }
    public function createdBy(): UserId { return $this->createdBy; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
