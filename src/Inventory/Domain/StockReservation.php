<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class StockReservation
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $productId,
        private string $orderReference,
        private int $requestedQuantity,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt,
    ) {
        if (trim($orderReference) === '' || mb_strlen($orderReference) > 100) {
            throw new InvalidArgumentException('An order reference must contain 1 to 100 characters.');
        }
        if ($requestedQuantity <= 0) {
            throw new InvalidArgumentException('A reservation quantity must be greater than zero.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function productId(): InventoryId { return $this->productId; }
    public function orderReference(): string { return trim($this->orderReference); }
    public function requestedQuantity(): int { return $this->requestedQuantity; }
    public function createdBy(): UserId { return $this->createdBy; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
