<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Site;

enum SiteStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
