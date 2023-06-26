<?php

declare(strict_types=1);

namespace WebWMS\Service\User\UserRole;

use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRole;
use WebWMS\Service\DataHandlers\User\UserRole\UserRoleDataHandler;

/**
 * @package:    WebWMS\Service\User\UserRole
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleService
 */
class UserRoleService
{
    public function __construct(
        private readonly UserRoleDataHandler $userRoleDataHandler
    ) {
    }

    public function getUserRoleByUserRoleName(string $userRoleName): ?UserRole
    {
        return $this->userRoleDataHandler->getUserRoleByUserRoleName($userRoleName);
    }

    public function getUserRoleById(int $userRoleId): ?UserRole
    {
        return $this->userRoleDataHandler->getUserRoleById($userRoleId);
    }

    /**
     * @return array<int, UserRole>
     */
    public function getAllUserRoles(): array
    {
        return $this->userRoleDataHandler->getAllUserRoles();
    }

    public function addUserRole(Request $request): void
    {
        $this->userRoleDataHandler->addUserRole($request);
    }

    public function updateUserRole(Request $request): ?UserRole
    {
        return $this->userRoleDataHandler->updateUserRole($request);
    }

    public function deleteUserRole(string $username): void
    {
        $this->userRoleDataHandler->deleteUserRole($username);
    }
}
