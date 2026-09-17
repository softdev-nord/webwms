<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\UserController',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserDataHandler'
)]
readonly class UserDataHandler implements PasswordUpgraderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
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

        $result = $queryBuilder->executeQuery();

        $results = $result->fetchAllAssociative();

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

    public function addUser(User $user): void
    {
        $user->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($user);
    }

    public function updateUser(User $user): void
    {
        $user->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($user);
    }

    public function deleteUser(string $username): void
    {
        $user = $this->getUserByUsername($username);

        if ($user instanceof User) {
            $this->delete($user);
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
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
        /** @var User|null $selectedUser */
        $selectedUser = $this->entityManager
            ->getRepository(User::class)
            ->find($user->getId());

        if (!$selectedUser instanceof User) {
            return;
        }

        $selectedUser->setLastLogin($this->dateTimeService->createDateTime());

        $this->save($selectedUser);
    }
}
