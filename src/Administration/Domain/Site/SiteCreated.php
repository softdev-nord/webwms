<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Site;

use DateTimeImmutable;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Shared\Domain\Event\DomainEvent;

final readonly class SiteCreated implements DomainEvent
{
    public function __construct(
        private SiteId $siteId,
        private TenantId $tenantId,
        private SiteCode $code,
        private DateTimeImmutable $occurredAt,
    ) {
    }

    public function siteId(): SiteId
    {
        return $this->siteId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function code(): SiteCode
    {
        return $this->code;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
