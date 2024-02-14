<?php

declare(strict_types=1);

namespace WebWMS\Service\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserEntity;
use WebWMS\Service\DataHandlers\User\UserDataHandler;

/**
 * @package:    WebWMS\Service\UserController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserService
 */
class UserService
{
    public function __construct(
        private readonly UserDataHandler $userDataHandler
    ) {
    }

    public function getUserByUsername(string $username): ?UserEntity
    {
        return $this->userDataHandler->getUserByUsername($username);
    }

    public function getUserById(int $userId): ?UserEntity
    {
        return $this->userDataHandler->getUserById($userId);
    }

    /**
     * @throws Exception
     */
    public function getAllUsers(): JsonResponse
    {
        return $this->userDataHandler->getAllUsers();
    }

    public function addUser(Request $request): void
    {
        $this->userDataHandler->addUser($request);
    }

    /**
     * @return object[]
     */
    public function getLastUser(): array
    {
        return $this->userDataHandler->getLastUser();
    }

    public function updateUser(Request $request): ?UserEntity
    {
        return $this->userDataHandler->updateUser($request);
    }

    public function upgradePassword(UserEntity $userEntity, string $newHashedPassword): void
    {
        $this->userDataHandler->upgradePassword($userEntity, $newHashedPassword);
    }

    public function deleteUser(string $username): void
    {
        $this->userDataHandler->deleteUser($username);
    }

    public function updateLastLogin(UserEntity $userEntity): void
    {
        $this->userDataHandler->updateLastLogin($userEntity);
    }
}
