<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Site\CreateSite;

use DateTimeImmutable;

final readonly class CreateSiteCommand
{
    public function __construct(
        public string $siteId,
        public string $tenantId,
        public string $code,
        public string $name,
        public string $timezone,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
