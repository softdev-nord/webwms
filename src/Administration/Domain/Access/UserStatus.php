<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
