<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InboundDiscrepancyResolution
{
    public function __construct(
        private InventoryId $receiptId,
        private TenantId $tenantId,
        private string $action,
        private string $note,
        private InventoryId $transferId,
        private InventoryId $sourceLedgerId,
        private InventoryId $destinationLedgerId,
        private UserId $resolvedBy,
        private DateTimeImmutable $resolvedAt,
    ) {
        if (!in_array($action, ['release', 'reject'], true)) {
            throw new InvalidArgumentException('The discrepancy action must be release or reject.');
        }
        if (trim($note) === '' || mb_strlen(trim($note)) > 255) {
            throw new InvalidArgumentException('A discrepancy resolution note must contain 1 to 255 characters.');
        }
    }

    public function receiptId(): InventoryId
    {
        return $this->receiptId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function note(): string
    {
        return trim($this->note);
    }

    public function transferId(): InventoryId
    {
        return $this->transferId;
    }

    public function sourceLedgerId(): InventoryId
    {
        return $this->sourceLedgerId;
    }

    public function destinationLedgerId(): InventoryId
    {
        return $this->destinationLedgerId;
    }

    public function resolvedBy(): UserId
    {
        return $this->resolvedBy;
    }

    public function resolvedAt(): DateTimeImmutable
    {
        return $this->resolvedAt;
    }
}
