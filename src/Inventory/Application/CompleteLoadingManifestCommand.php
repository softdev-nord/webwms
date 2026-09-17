<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;

final readonly class CompleteLoadingManifestCommand
{
    public function __construct(public string $manifestId, public string $tenantId, public string $completedBy, public DateTimeImmutable $completedAt) {}
}
