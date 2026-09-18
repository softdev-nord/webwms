<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class InboundDelivery
{
    /** @param list<InboundDeliveryLine> $lines */
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private InventoryId $purchaseOrderId,
        private string $code,
        private string $deliveryNote,
        private DateTimeImmutable $expectedAt,
        private array $lines,
        private UserId $createdBy,
        private DateTimeImmutable $createdAt
    ) {
        foreach ([$code, $deliveryNote] as $value) {
            if (trim($value) === '' || mb_strlen($value) > 80) {
                throw new InvalidArgumentException('Advice code and delivery note must contain 1 to 80 characters.');
            }
        }
        if ($lines === []) {
            throw new InvalidArgumentException('An inbound delivery requires at least one line.');
        }
    }

    public function id(): InventoryId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function purchaseOrderId(): InventoryId
    {
        return $this->purchaseOrderId;
    }

    public function code(): string
    {
        return mb_strtoupper(trim($this->code));
    }

    public function deliveryNote(): string
    {
        return trim($this->deliveryNote);
    }

    public function expectedAt(): DateTimeImmutable
    {
        return $this->expectedAt;
    }

    /** @return list<InboundDeliveryLine> */
    public function lines(): array
    {
        return $this->lines;
    }

    public function createdBy(): UserId
    {
        return $this->createdBy;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
