<?php

declare(strict_types=1);

namespace WebWMS\Installation\Application;

final readonly class InstallationResult
{
    /** @param array<string, bool> $checks */
    public function __construct(
        public string $tenantId,
        public string $siteId,
        public string $adminId,
        public int $storageLocationCount,
        public array $checks,
    ) {
    }
}
