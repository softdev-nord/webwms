<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Tenant\CreateTenant;

use DateTimeImmutable;

final readonly class CreateTenantCommand
{
    public function __construct(
        public string $tenantId,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
