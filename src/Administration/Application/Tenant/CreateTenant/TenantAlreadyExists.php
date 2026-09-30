<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Tenant\CreateTenant;

use DomainException;

class TenantAlreadyExists extends DomainException
{
}
