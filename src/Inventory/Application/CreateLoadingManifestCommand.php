<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CreateLoadingManifestCommand
{
    /** @param list<string> $shipmentIds */
    public function __construct(public string $manifestId, public string $tenantId, public string $code, public string $tourReference, public string $vehicleReference, public array $shipmentIds, public string $createdBy, public DateTimeImmutable $createdAt) {}
}
