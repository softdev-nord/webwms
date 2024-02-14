<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User\UserRight;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRightEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\UserRightEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRightDataHandler
 */
class UserRightDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(UserRightEntity $userRightEntity): void
    {
        $this->entityManager->persist($userRightEntity);
        $this->entityManager->flush();
    }

    public function delete(UserRightEntity $userRightEntity): void
    {
        $this->entityManager->remove($userRightEntity);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, UserRightEntity>
     */
    public function getAllUserRights(): array
    {
        return $this->entityManager
            ->getRepository(UserRightEntity::class)
            ->findAll();
    }

    public function getUserRightById(int $userRightId): ?UserRightEntity
    {
        return $this->entityManager
            ->getRepository(UserRightEntity::class)
            ->findOneBy(['id' => $userRightId]);
    }

    public function getUserRightByUserRightName(string $userRightName): ?UserRightEntity
    {
        return $this->entityManager
            ->getRepository(UserRightEntity::class)
            ->findOneBy(['user_right' => $userRightName]);
    }

    public function addUserRight(Request $request): ?UserRightEntity
    {
        $addUserRight = $request->request->getIterator()->getArrayCopy();
        $userRightEntity = new UserRightEntity();

        $userRightEntity->setUserRight($addUserRight['user_right']);
        $userRightEntity->setDescription($addUserRight['description']);
        $userRightEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($userRightEntity);

        return $userRightEntity;
    }

    public function updateUserRight(Request $request): ?UserRightEntity
    {
        $requestData = $request->request->all()['edit_user_right'];
        $userRight = $this->entityManager
            ->getRepository(UserRightEntity::class)
            ->findOneBy(['user_right' => $requestData['user_right']]);

        if ($userRight === null) {
            return null;
        }

        $userRight->setUserRight($requestData['user_right']);
        $userRight->setDescription($requestData['description']);
        $userRight->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($userRight);

        return $userRight;
    }

    public function deleteUserRight(string $userRightName): void
    {
        $userRight = $this->getUserRightByUserRightName($userRightName);

        if ($userRight instanceof UserRightEntity) {
            $this->delete($userRight);
        }
    }
}
