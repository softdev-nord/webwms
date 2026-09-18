<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateReplenishmentPolicyCommand
{
    public function __construct(public string $policyId, public string $tenantId, public string $warehouseId, public string $productId, public string $targetLocationId, public string $code, public string $sourceLocationPrefix, public int $minimumQuantity, public int $targetQuantity, public int $priority, public string $createdBy, public DateTimeImmutable $createdAt) {}
}
