<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class ReturnInspection
{
    public function __construct(
        private InventoryId $receiptId,
        private InventoryId $ledgerEntryId,
        private TenantId $tenantId,
        private InventoryId $locationId,
        private ReturnQualityDecision $decision,
        private string $note,
        private StockDimensions $dimensions,
        private UserId $inspectedBy,
        private DateTimeImmutable $inspectedAt
    ) {
        if (trim($note) === '' || mb_strlen($note) > 255) {
            throw new InvalidArgumentException('An inspection note must contain 1 to 255 characters.');
        }
    }

    public function receiptId(): InventoryId
    {
        return $this->receiptId;
    }

    public function ledgerEntryId(): InventoryId
    {
        return $this->ledgerEntryId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function locationId(): InventoryId
    {
        return $this->locationId;
    }

    public function decision(): ReturnQualityDecision
    {
        return $this->decision;
    }

    public function note(): string
    {
        return trim($this->note);
    }

    public function dimensions(): StockDimensions
    {
        return new StockDimensions($this->decision->stockStatus(), $this->dimensions->batchNumber(), $this->dimensions->serialNumber(), $this->dimensions->expiresAt());
    }

    public function inspectedBy(): UserId
    {
        return $this->inspectedBy;
    }

    public function inspectedAt(): DateTimeImmutable
    {
        return $this->inspectedAt;
    }
}
