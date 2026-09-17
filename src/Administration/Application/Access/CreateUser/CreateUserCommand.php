<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Access\CreateUser;

use DateTimeImmutable;

final readonly class CreateUserCommand
{
    /** @param list<string> $roleIds */
    public function __construct(
        public string $userId,
        public string $tenantId,
        public string $email,
        public string $displayName,
        public string $passwordHash,
        public array $roleIds,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
