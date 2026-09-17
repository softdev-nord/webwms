<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Tenant;

use DateTimeImmutable;
use WebWMS\Shared\Domain\Event\DomainEvent;

final readonly class TenantCreated implements DomainEvent
{
    public function __construct(
        private TenantId $tenantId,
        private string $name,
        private DateTimeImmutable $occurredAt,
    ) {
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
