<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User\UserRight;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRight;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\UserRight
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

    public function save(UserRight $userRight): void
    {
        $this->entityManager->persist($userRight);
        $this->entityManager->flush();
    }

    public function delete(UserRight $userRight): void
    {
        $this->entityManager->remove($userRight);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, UserRight>
     */
    public function getAllUserRights(): array
    {
        return $this->entityManager
            ->getRepository(UserRight::class)
            ->findAll();
    }

    public function getUserRightById(int $userRightId): ?UserRight
    {
        return $this->entityManager
            ->getRepository(UserRight::class)
            ->findOneBy(['id' => $userRightId]);
    }

    public function getUserRightByUserRightName(string $userRightName): ?UserRight
    {
        return $this->entityManager
            ->getRepository(UserRight::class)
            ->findOneBy(['user_right' => $userRightName]);
    }

    public function addUserRight(Request $request): ?UserRight
    {
        $addUserRight = $request->request->getIterator()->getArrayCopy();
        $userRight = new UserRight();

        $userRight->setUserRight($addUserRight['user_right']);
        $userRight->setDescription($addUserRight['description']);
        $userRight->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($userRight);

        return $userRight;
    }

    public function updateUserRight(Request $request): ?UserRight
    {
        $requestData = $request->request->all()['edit_user_right'];
        $userRight = $this->entityManager
            ->getRepository(UserRight::class)
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

        if ($userRight !== null) {
            $this->delete($userRight);
        }
    }
}
