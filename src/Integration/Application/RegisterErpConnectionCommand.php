<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;

final readonly class RegisterErpConnectionCommand
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public string $endpointUrl,
        public string $credentialEnv,
        public bool $active,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
    }
}
