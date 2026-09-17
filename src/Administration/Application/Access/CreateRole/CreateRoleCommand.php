<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Access\CreateRole;

use DateTimeImmutable;

final readonly class CreateRoleCommand
{
    /** @param list<string> $permissions */
    public function __construct(
        public string $roleId,
        public string $tenantId,
        public string $code,
        public string $name,
        public array $permissions,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
