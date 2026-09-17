<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Tenant;

enum TenantStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
