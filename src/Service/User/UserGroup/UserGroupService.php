<?php

declare(strict_types=1);

namespace WebWMS\Service\User\UserGroup;

use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserGroupEntity;
use WebWMS\Service\DataHandlers\User\UserGroup\UserGroupDataHandler;

/**
 * @package:    WebWMS\Service\UserController\UserGroupEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserGroupService
 */
class UserGroupService
{
    public function __construct(
        private readonly UserGroupDataHandler $userGroupDataHandler
    ) {
    }

    public function getUserGroupByUserGroupName(string $userGroupName): ?UserGroupEntity
    {
        return $this->userGroupDataHandler->getUserGroupByUserGroupName($userGroupName);
    }

    public function getUserGroupById(int $userGroupId): ?UserGroupEntity
    {
        return $this->userGroupDataHandler->getUserGroupById($userGroupId);
    }

    /**
     * @return array<int, UserGroupEntity>
     */
    public function getAllUserGroups(): array
    {
        return $this->userGroupDataHandler->getAllUserGroups();
    }

    public function addUserGroup(Request $request): void
    {
        $this->userGroupDataHandler->addUserGroup($request);
    }

    public function updateUserGroup(Request $request): ?UserGroupEntity
    {
        return $this->userGroupDataHandler->updateUserGroup($request);
    }

    public function deleteUserGroup(string $username): void
    {
        $this->userGroupDataHandler->deleteUserGroup($username);
    }
}
