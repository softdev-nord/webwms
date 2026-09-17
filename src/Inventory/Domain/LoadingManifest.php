<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class LoadingManifest
{
    /** @param list<InventoryId> $shipmentIds */
    public function __construct(private InventoryId $id, private TenantId $tenantId, private string $code, private string $tourReference, private string $vehicleReference, private array $shipmentIds, private UserId $createdBy, private DateTimeImmutable $createdAt)
    {
        foreach ([$code, $tourReference, $vehicleReference] as $value) {
            if (trim($value) === '' || mb_strlen($value) > 80) {
                throw new InvalidArgumentException('Manifest code, tour and vehicle must contain 1 to 80 characters.');
            }
        }
        if ($shipmentIds === []) {
            throw new InvalidArgumentException('A loading manifest requires at least one shipment.');
        }
        $unique = array_unique(array_map(static fn (InventoryId $id): string => $id->value(), $shipmentIds));
        if (count($unique) !== count($shipmentIds)) {
            throw new InvalidArgumentException('A shipment may only occur once in a manifest.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function code(): string { return mb_strtoupper(trim($this->code)); }
    public function tourReference(): string { return trim($this->tourReference); }
    public function vehicleReference(): string { return trim($this->vehicleReference); }
    /** @return list<InventoryId> */
    public function shipmentIds(): array { return $this->shipmentIds; }
    public function createdBy(): UserId { return $this->createdBy; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
