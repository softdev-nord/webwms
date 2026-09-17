<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreatePackingOrderCommand
{
    public function __construct(public string $orderId, public string $tenantId, public string $pickListId, public string $code, public string $createdBy, public DateTimeImmutable $createdAt) {}
}
