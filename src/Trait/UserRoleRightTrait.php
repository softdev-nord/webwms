<?php

declare(strict_types=1);

namespace WebWMS\Trait;

use WebWMS\Security\UserRoleRight;

trait UserRoleRightTrait
{
    public function __construct(
        private readonly UserRoleRight $userRoleRight
    ) {
    }
}
