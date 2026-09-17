<?php

declare(strict_types=1);

namespace WebWMS\Shared\Domain\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredAt(): DateTimeImmutable;
}
