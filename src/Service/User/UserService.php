<?php

declare(strict_types=1);

namespace WebWMS\Service\User;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\User;
use WebWMS\Service\DataHandlers\User\UserDataHandler;

/**
 * @package:    WebWMS\Service\User
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        UserService
 */
class UserService
{
    public function __construct(
        private UserDataHandler $userDataHandler
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

    public function updateUser(Request $request): ?User
    {
        return $this->userDataHandler->updateUser($request);
    }

    public function upgradePassword(UserInterface|PasswordAuthenticatedUserInterface $user, $newHashedPassword): void
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
