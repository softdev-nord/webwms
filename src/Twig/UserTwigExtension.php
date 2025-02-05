<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WebWMS\Entity\UserRight;
use WebWMS\Entity\UserRole;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Security\UserRoleRight;

#[ClassInformation(
    package: 'WebWMS\Twig',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserTwigExtension'
)]
class UserTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly UserRoleRight $userRoleRight,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_role', $this->hasUserRole(...)),
            new TwigFunction('has_right', $this->hasUserRight(...)),
            new TwigFunction('has_group', $this->hasUserGroup(...)),
        ];
    }

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

    public function roleHasRight(UserRole $userRole, UserRight $userRight): bool
    {
        return in_array($userRight->getUserRight(), $userRole->getUserRights(), true);
    }
}
