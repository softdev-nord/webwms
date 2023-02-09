<?php

// /src/Twig/AccessCheckRuntime.php
declare(strict_types=1);

namespace WebWMS\Twig;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AccessCheckRuntime implements RuntimeExtensionInterface
{
    /**
     * this uses PHP 8.0 constructor property promotion to reduce the needed lines.
     **/
    public function __construct(
        protected RoleHierarchyInterface $roleHierarchy
    ) {
    }

    public function hasRole(UserInterface $user, string $role): bool
    {
        return in_array($role, $this->roleHierarchy->getReachableRoleNames($user->getRoles()));
    }
}
