<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateReturnOrderCommand
{
    /** @param list<array{id: string, productId: string, quantity: int, reason: string}> $items */
    public function __construct(public string $returnOrderId, public string $tenantId, public string $code, public string $orderReference, public array $items, public string $createdBy, public DateTimeImmutable $createdAt) {}
}
