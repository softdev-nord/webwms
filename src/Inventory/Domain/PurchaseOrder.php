<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PurchaseOrder
{
    /** @param list<PurchaseOrderItem> $items */
    public function __construct(private InventoryId $id, private TenantId $tenantId, private string $code, private string $supplierReference, private array $items, private UserId $createdBy, private DateTimeImmutable $createdAt)
    {
        foreach ([$code, $supplierReference] as $value) {
            if (trim($value) === '' || mb_strlen($value) > 80) {
                throw new InvalidArgumentException('Purchase order code and supplier reference must contain 1 to 80 characters.');
            }
        }
        if ($items === []) {
            throw new InvalidArgumentException('A purchase order requires at least one item.');
        }
    }
    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function code(): string { return mb_strtoupper(trim($this->code)); }
    public function supplierReference(): string { return trim($this->supplierReference); }
    /** @return list<PurchaseOrderItem> */
    public function items(): array { return $this->items; }
    public function createdBy(): UserId { return $this->createdBy; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
