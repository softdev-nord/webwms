<?php

declare(strict_types=1);

namespace WebWMS\Trait;

use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Security\UserRoleRight;

#[ClassInformation(
    package: 'WebWMS\Trait',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    trait: 'UserRoleRightTrait'
)]
trait UserRoleRightTrait
{
    public function __construct(
        private readonly UserRoleRight $userRoleRight
    ) {
    }
}
