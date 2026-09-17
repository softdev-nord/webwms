<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Tenant\CreateTenant;

use DomainException;

final class TenantAlreadyExists extends DomainException
{
}
