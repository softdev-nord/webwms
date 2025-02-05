<?php

declare(strict_types=1);

namespace WebWMS\Service\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\User\UserDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserService'
)]
readonly class UserService
{
    public function __construct(
        private UserDataHandler $userDataHandler,
    ) {
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->userDataHandler->getUserByUsername($username);
    }

    public function getUserById(int $userId): ?User
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

    public function addUser(User $user): void
    {
        $this->userDataHandler->addUser($user);
    }

    /**
     * @return object[]
     */
    public function getLastUser(): array
    {
        return $this->userDataHandler->getLastUser();
    }

    public function updateUser(User $user): void
    {
        $this->userDataHandler->updateUser($user);
    }

    public function upgradePassword(User $user, string $newHashedPassword): void
    {
        $this->userDataHandler->upgradePassword($user, $newHashedPassword);
    }

    public function deleteUser(string $username): void
    {
        $this->userDataHandler->deleteUser($username);
    }

    public function updateLastLogin(User $user): void
    {
        $this->userDataHandler->updateLastLogin($user);
    }
}
