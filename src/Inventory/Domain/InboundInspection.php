<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InboundInspection
{
    /** @param list<QualityCheckAnswer> $answers */
    public function __construct(
        private InventoryId $receiptId,
        private InventoryId $ledgerEntryId,
        private TenantId $tenantId,
        private InventoryId $locationId,
        private InboundQualityDecision $decision,
        private array $answers,
        private StockDimensions $dimensions,
        private UserId $inspectedBy,
        private DateTimeImmutable $inspectedAt
    ) {
        if ($answers === []) {
            throw new InvalidArgumentException('An inbound inspection requires a quality checklist.');
        }
        if ($decision === InboundQualityDecision::Accept && array_any($answers, static fn (QualityCheckAnswer $answer): bool => !$answer->passed())) {
            throw new InvalidArgumentException('An inbound receipt with failed checks cannot be accepted.');
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

    public function decision(): InboundQualityDecision
    {
        return $this->decision;
    }

    /** @return list<QualityCheckAnswer> */
    public function answers(): array
    {
        return $this->answers;
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
