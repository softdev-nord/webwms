<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InventoryCountApproval
{
    /** @param array<string, InventoryId> $ledgerEntryIds */
    public function __construct(private InventoryId $countId, private TenantId $tenantId, private array $ledgerEntryIds, private UserId $approvedBy, private DateTimeImmutable $approvedAt)
    {
        if (count(array_unique(array_map(static fn (InventoryId $id): string => $id->value(), $ledgerEntryIds))) !== count($ledgerEntryIds)) {
            throw new InvalidArgumentException('Every inventory adjustment needs a unique ledger entry ID.');
        }
    }
    public function countId(): InventoryId { return $this->countId; }
    public function tenantId(): TenantId { return $this->tenantId; }
    /** @return array<string, InventoryId> */
    public function ledgerEntryIds(): array { return $this->ledgerEntryIds; }
    public function approvedBy(): UserId { return $this->approvedBy; }
    public function approvedAt(): DateTimeImmutable { return $this->approvedAt; }
}
