<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateInboundDeliveryCommand
{
    /** @param list<array{id: string, purchaseOrderItemId: string, quantity: int}> $lines */
    public function __construct(public string $deliveryId, public string $tenantId, public string $purchaseOrderId, public string $code, public string $deliveryNote, public DateTimeImmutable $expectedAt, public array $lines, public string $createdBy, public DateTimeImmutable $createdAt) {}
}
