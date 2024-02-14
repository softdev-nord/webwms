<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User\UserGroup;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserGroupEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\UserGroupEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserGroupDataHandler
 */
class UserGroupDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(UserGroupEntity $userGroupEntity): void
    {
        $this->entityManager->persist($userGroupEntity);
        $this->entityManager->flush();
    }

    public function delete(UserGroupEntity $userGroupEntity): void
    {
        $this->entityManager->remove($userGroupEntity);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, UserGroupEntity>
     */
    public function getAllUserGroups(): array
    {
        return $this->entityManager
            ->getRepository(UserGroupEntity::class)
            ->findAll();
    }

    public function getUserGroupById(int $userGroupId): ?UserGroupEntity
    {
        return $this->entityManager
            ->getRepository(UserGroupEntity::class)
            ->findOneBy(['id' => $userGroupId]);
    }

    public function getUserGroupByUserGroupName(string $userGroupName): ?UserGroupEntity
    {
        return $this->entityManager
            ->getRepository(UserGroupEntity::class)
            ->findOneBy(['group' => $userGroupName]);
    }

    public function addUserGroup(Request $request): ?UserGroupEntity
    {
        $addUserGroup = $request->request->getIterator()->getArrayCopy();
        $userGroupEntity = new UserGroupEntity();

        $userGroupEntity->setGroup($addUserGroup['group']);
        $userGroupEntity->setDescription($addUserGroup['description']);
        $userGroupEntity->setRoles($addUserGroup['roles']);
        $userGroupEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($userGroupEntity);

        return $userGroupEntity;
    }

    public function updateUserGroup(Request $request): ?UserGroupEntity
    {
        $requestData = $request->request->all()['edit_user_group'];
        $userGroup = $this->entityManager
            ->getRepository(UserGroupEntity::class)
            ->findOneBy(['group' => $requestData['group']]);

        if ($userGroup === null) {
            return null;
        }

        $roles = $requestData['roles'];

        $userGroup->setGroup($requestData['group']);
        $userGroup->setDescription($requestData['description']);
        $userGroup->setRoles($roles);
        $userGroup->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($userGroup);

        return $userGroup;
    }

    public function deleteUserGroup(string $userGroupName): void
    {
        $userGroup = $this->getUserGroupByUserGroupName($userGroupName);

        if ($userGroup instanceof UserGroupEntity) {
            $this->delete($userGroup);
        }
    }
}
