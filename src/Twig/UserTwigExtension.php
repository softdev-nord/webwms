<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WebWMS\Entity\UserRightEntity;
use WebWMS\Entity\UserRoleEntity;
use WebWMS\Security\UserRoleRight;

/**
 * @package:    WebWMS\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserTwigExtension
 */
class UserTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly UserRoleRight $userRoleRight
    ) {
    }

    #[Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_role', $this->hasUserRole(...)),
            new TwigFunction('has_right', $this->hasUserRight(...)),
            new TwigFunction('has_group', $this->hasUserGroup(...)),
        ];
    }

    #[Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('roleHasRight', $this->roleHasRight(...)),
        ];
    }

    public function hasUserRole(string $userRole): bool
    {
        return $this->userRoleRight->hasUserRole($userRole);
    }

    public function hasUserRight(string $userRight): bool
    {
        return $this->userRoleRight->hasUserRight($userRight);
    }

    public function hasUserGroup(string $userGroup): bool
    {
        return $this->userRoleRight->hasUserGroup($userGroup);
    }

    public function roleHasRight(UserRoleEntity $userRoleEntity, UserRightEntity $userRightEntity): bool
    {
        return in_array($userRightEntity->getUserRight(), $userRoleEntity->getUserRights(), true);
    }
}
