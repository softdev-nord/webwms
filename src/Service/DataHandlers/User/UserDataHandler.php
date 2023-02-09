<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use WebWMS\Entity\User;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\User
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        UserDataHandler
 */
class UserDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(User $user): void
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
        $requestData = $request->request->all()['add_user'];
        $user = new User();

        $user->setUsername((string) $requestData['username']);
        $user->setFirstname((string) $requestData['firstname']);
        $user->setLastname((string) $requestData['firstname']);
        $user->setPassword((string) $requestData['password']);
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

        if (!$user) {
            return null;
        }

        $user->setUsername((string) $requestData['username']);
        $user->setRoles((array) $requestData['roles']);
        $user->setFirstname((string) $requestData['firstname']);
        $user->setLastname((string) $requestData['lastname']);
        $user->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->update($user);

        return $user;
    }

    public function deleteUser(string $username): void
    {
        $user = $this->getUserByUsername($username);

        if ($user) {
            $this->delete($user);
        }
    }

    public function updateUserPassword(Request $request): ?User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $request->get('username')]);

        if (!$user) {
            return null;
        }

        if ($user->getPassword() === $request->get('oldPassword')) {
            // encode the plain password
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $request->get('newPassword')
                )
            );
        }

        $this->update($user);

        return $user;
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
}
