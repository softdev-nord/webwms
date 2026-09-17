<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Shared\Domain\Event\DomainEvent;

final readonly class RoleCreated implements DomainEvent
{
    public function __construct(
        private RoleId $roleId,
        private TenantId $tenantId,
        private DateTimeImmutable $occurredAt,
    ) {
    }

    public function roleId(): RoleId
    {
        return $this->roleId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
