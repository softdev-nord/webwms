<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PutawayStrategy
{
    public function __construct(private InventoryId $id, private TenantId $tenantId, private InventoryId $warehouseId, private string $code, private StockStatus $stockStatus, private string $locationPrefix, private int $priority, private UserId $createdBy, private DateTimeImmutable $createdAt)
    {
        if (trim($code) === '' || mb_strlen($code) > 50) {
            throw new InvalidArgumentException('A putaway strategy code must contain 1 to 50 characters.');
        }
        if (preg_match('/^[A-Z0-9][A-Z0-9._-]{0,49}$/', $locationPrefix) !== 1) {
            throw new InvalidArgumentException('A target location prefix must use uppercase location characters.');
        }
        if ($priority < 1) {
            throw new InvalidArgumentException('A putaway strategy priority must be positive.');
        }
    }
    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function warehouseId(): InventoryId { return $this->warehouseId; }
    public function code(): string { return mb_strtoupper(trim($this->code)); }
    public function stockStatus(): StockStatus { return $this->stockStatus; }
    public function locationPrefix(): string { return $this->locationPrefix; }
    public function priority(): int { return $this->priority; }
    public function createdBy(): UserId { return $this->createdBy; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
