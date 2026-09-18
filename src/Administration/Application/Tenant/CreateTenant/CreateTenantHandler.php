<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Tenant\CreateTenant;

use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final readonly class CreateTenantHandler
{
    public function __construct(
        private TenantRepository $tenants
    ) {
    }

    public function __invoke(CreateTenantCommand $command): Tenant
    {
        $tenantId = new TenantId($command->tenantId);

        if ($this->tenants->exists($tenantId)) {
            throw new TenantAlreadyExists(sprintf('Tenant "%s" already exists.', $tenantId->value()));
        }

        $tenant = Tenant::create($tenantId, $command->name, $command->occurredAt);
        $this->tenants->save($tenant);

        return $tenant;
    }
}
