<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User\UserRight;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRight;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\UserRight',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserRightDataHandler'
)]
readonly class UserRightDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
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
        /** @var UserRight[] $userRights */
        $userRights = $this->entityManager
            ->getRepository(UserRight::class)
            ->findAll();

        return $userRights;
    }

    public function getUserRightById(int $userRightId): ?UserRight
    {
        /** @var UserRight|null $userRight */
        $userRight = $this->entityManager
            ->getRepository(UserRight::class)
            ->findOneBy(['id' => $userRightId]);

        return $userRight;
    }

    public function getUserRightByUserRightName(string $userRightName): ?UserRight
    {
        /** @var UserRight|null $userRight */
        $userRight = $this->entityManager
            ->getRepository(UserRight::class)
            ->findOneBy(['user_right' => $userRightName]);

        return $userRight;
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
        /** @var UserRight|null $userRight */
        $userRight = $this->entityManager
            ->getRepository(UserRight::class)
            ->findOneBy(['user_right' => $requestData['user_right']]);

        if (!$userRight instanceof UserRight) {
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

        if ($userRight instanceof UserRight) {
            $this->delete($userRight);
        }
    }
}
