<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Site\CreateSite;

use WebWMS\Administration\Domain\Site\Site;
use WebWMS\Administration\Domain\Site\SiteCode;
use WebWMS\Administration\Domain\Site\SiteId;
use WebWMS\Administration\Domain\Site\SiteRepository;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final readonly class CreateSiteHandler
{
    public function __construct(
        private TenantRepository $tenants,
        private SiteRepository $sites,
    ) {
    }

    public function __invoke(CreateSiteCommand $command): Site
    {
        $tenantId = new TenantId($command->tenantId);

        if (!$this->tenants->exists($tenantId)) {
            throw new TenantNotFound(sprintf('Tenant "%s" does not exist.', $tenantId->value()));
        }

        $siteCode = new SiteCode($command->code);

        if ($this->sites->existsForTenant($tenantId, $siteCode)) {
            throw new SiteCodeAlreadyExists(sprintf(
                'Site code "%s" already exists for tenant "%s".',
                $siteCode->value(),
                $tenantId->value(),
            ));
        }

        $site = Site::create(
            new SiteId($command->siteId),
            $tenantId,
            $siteCode,
            $command->name,
            $command->timezone,
            $command->occurredAt,
        );
        $this->sites->save($site);

        return $site;
    }
}
