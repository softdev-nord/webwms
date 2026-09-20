<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class UnplannedReceipt
{
    /** @param list<UnplannedReceiptItem> $items */
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private string $code,
        private string $supplierCode,
        private string $supplierName,
        private ?string $deliveryNote,
        private array $items,
        private UserId $acceptedBy,
        private DateTimeImmutable $acceptedAt,
    ) {
        if (trim($code) === '' || trim($supplierCode) === '' || trim($supplierName) === '') {
            throw new InvalidArgumentException('An unplanned receipt requires code and supplier identification.');
        }
        if ($items === []) {
            throw new InvalidArgumentException('An unplanned receipt requires at least one item.');
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

    public function code(): string
    {
        return trim($this->code);
    }

    public function supplierCode(): string
    {
        return mb_strtoupper(trim($this->supplierCode));
    }

    public function supplierName(): string
    {
        return trim($this->supplierName);
    }

    public function deliveryNote(): ?string
    {
        return $this->deliveryNote === null ? null : trim($this->deliveryNote);
    }

    /** @return list<UnplannedReceiptItem> */
    public function items(): array
    {
        return $this->items;
    }

    public function acceptedBy(): UserId
    {
        return $this->acceptedBy;
    }

    public function acceptedAt(): DateTimeImmutable
    {
        return $this->acceptedAt;
    }
}
