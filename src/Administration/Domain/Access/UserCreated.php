<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Shared\Domain\Event\DomainEvent;

final readonly class UserCreated implements DomainEvent
{
    public function __construct(
        private UserId $userId,
        private TenantId $tenantId,
        private DateTimeImmutable $occurredAt,
    ) {
    }

    public function userId(): UserId
    {
        return $this->userId;
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
