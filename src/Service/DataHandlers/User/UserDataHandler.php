<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use WebWMS\Entity\Role;
use WebWMS\Entity\User;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\User
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserDataHandler
 */
class UserDataHandler implements PasswordUpgraderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function getAllUsers(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('user');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getUserById(int $userId): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['userId' => $userId]);
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
    }

    public function addUser(Request $request): ?User
    {
        $addUser = $request->request->getIterator()->getArrayCopy();
        $user = new User();

        $user->setUsername($addUser['username']);
        $user->setFirstname($addUser['firstname']);
        $user->setLastname($addUser['firstname']);
        $user->setPassword($addUser['password']);
        $user->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($user);

        return $user;
    }

    public function updateUser(Request $request): ?User
    {
        $requestData = $request->request->all()['edit_user'];
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $requestData['username']]);

        if ($user === null) {
            return null;
        }

        /** @var Role $roles */
        $roles = $requestData['roles'];

        $user->setUsername(strval($requestData['username']));
        $user->setRole($roles);
        $user->setFirstname(strval($requestData['firstname']));
        $user->setLastname(strval($requestData['lastname']));
        $user->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($user);

        return $user;
    }

    public function deleteUser(string $username): void
    {
        $user = $this->getUserByUsername($username);

        if ($user !== null) {
            $this->delete($user);
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword($user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user);
    }

    /**
     * @return array<object>
     */
    public function getLastUser(): array
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findBy([], ['id' => 'DESC'], 1, 0);
    }

    public function updateLastLogin(User $user): void
    {
        $selectedUser = $this->entityManager
            ->getRepository(User::class)
            ->find($user->getId());

        if ($selectedUser === null) {
            return;
        }

        $selectedUser->setLastLogin($this->dateTimeService->createDateTime());

        $this->save($selectedUser);
    }
}
