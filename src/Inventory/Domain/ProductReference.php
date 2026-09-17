<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ProductReference
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private Sku $sku,
        private string $name,
        private DateTimeImmutable $createdAt,
    ) {
        if (trim($name) === '' || mb_strlen($name) > 255) {
            throw new InvalidArgumentException('A product name must contain 1 to 255 characters.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function sku(): Sku { return $this->sku; }
    public function name(): string { return trim($this->name); }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
