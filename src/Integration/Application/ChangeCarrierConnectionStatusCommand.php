<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;

final readonly class ChangeCarrierConnectionStatusCommand
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public bool $active,
        public string $actorId,
        public DateTimeImmutable $at
    ) {
    }
}
