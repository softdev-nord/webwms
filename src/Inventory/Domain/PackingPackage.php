<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class PackingPackage
{
    /** @param list<InventoryId> $pickTaskIds */
    public function __construct(private InventoryId $id, private InventoryId $packingOrderId, private TenantId $tenantId, private string $packageNumber, private int $weightGrams, private array $pickTaskIds, private UserId $packedBy, private DateTimeImmutable $packedAt)
    {
        if (trim($packageNumber) === '' || mb_strlen($packageNumber) > 50 || $weightGrams <= 0 || $pickTaskIds === []) {
            throw new InvalidArgumentException('A package requires a number, positive weight and at least one pick task.');
        }
        if (count(array_unique(array_map(static fn (InventoryId $id): string => $id->value(), $pickTaskIds))) !== count($pickTaskIds)) {
            throw new InvalidArgumentException('A pick task must occur only once within a package.');
        }
    }
    public function id(): InventoryId { return $this->id; }
    public function packingOrderId(): InventoryId { return $this->packingOrderId; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function packageNumber(): string { return mb_strtoupper(trim($this->packageNumber)); }
    public function weightGrams(): int { return $this->weightGrams; }
    /** @return list<InventoryId> */
    public function pickTaskIds(): array { return $this->pickTaskIds; }
    public function packedBy(): UserId { return $this->packedBy; }
    public function packedAt(): DateTimeImmutable { return $this->packedAt; }
}
