<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PickConfirmation
{
    public function __construct(private InventoryId $taskId, private TenantId $tenantId, private PickOutcome $outcome, private ?InventoryId $ledgerEntryId, private string $note, private UserId $confirmedBy, private DateTimeImmutable $confirmedAt)
    {
        if ($outcome === PickOutcome::Picked && $ledgerEntryId === null) {
            throw new InvalidArgumentException('A picked task requires a ledger entry ID.');
        }
        if (trim($note) === '' || mb_strlen($note) > 255) {
            throw new InvalidArgumentException('A pick confirmation note must contain 1 to 255 characters.');
        }
    }
    public function taskId(): InventoryId { return $this->taskId; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function outcome(): PickOutcome { return $this->outcome; }
    public function ledgerEntryId(): ?InventoryId { return $this->ledgerEntryId; }
    public function note(): string { return trim($this->note); }
    public function confirmedBy(): UserId { return $this->confirmedBy; }
    public function confirmedAt(): DateTimeImmutable { return $this->confirmedAt; }
}
