<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class StorageLocation
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $warehouseId,
        private string $code,
        private DateTimeImmutable $createdAt,
    ) {
        if (preg_match('/^[A-Z0-9][A-Z0-9._-]{1,49}$/', $code) !== 1) {
            throw new InvalidArgumentException('A storage location code must contain 2 to 50 uppercase characters.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function warehouseId(): InventoryId { return $this->warehouseId; }
    public function code(): string { return $this->code; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
