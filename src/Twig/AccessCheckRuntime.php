<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AccessCheckRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        protected RoleHierarchyInterface $roleHierarchy
    ) {
    }

    public function hasRole(UserInterface $user, string $role): bool
    {
        return in_array($role, $this->roleHierarchy->getReachableRoleNames($user->getRoles()), true);
    }
}
