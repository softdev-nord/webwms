<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use WebWMS\Entity\UserEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\UserController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserDataHandler
 */
class UserDataHandler implements PasswordUpgraderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(UserEntity $userEntity): void
    {
        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();
    }

    public function delete(UserEntity $userEntity): void
    {
        $this->entityManager->remove($userEntity);
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

    public function getUserById(int $userId): ?UserEntity
    {
        return $this->entityManager
            ->getRepository(UserEntity::class)
            ->findOneBy(['userId' => $userId]);
    }

    public function getUserByUsername(string $username): ?UserEntity
    {
        return $this->entityManager
            ->getRepository(UserEntity::class)
            ->findOneBy(['username' => $username]);
    }

    public function addUser(UserEntity $userEntity): void
    {
        $userEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($userEntity);
    }

    public function updateUser(UserEntity $userEntity): void
    {
        $userEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($userEntity);
    }

    public function deleteUser(string $username): void
    {
        $user = $this->getUserByUsername($username);

        if ($user instanceof UserEntity) {
            $this->delete($user);
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    #[Override]
    public function upgradePassword($user, string $newHashedPassword): void
    {
        if (!$user instanceof UserEntity) {
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
            ->getRepository(UserEntity::class)
            ->findBy([], ['id' => 'DESC'], 1, 0);
    }

    public function updateLastLogin(UserEntity $userEntity): void
    {
        $selectedUser = $this->entityManager
            ->getRepository(UserEntity::class)
            ->find($userEntity->getId());

        if ($selectedUser === null) {
            return;
        }

        $selectedUser->setLastLogin($this->dateTimeService->createDateTime());

        $this->save($selectedUser);
    }
}
