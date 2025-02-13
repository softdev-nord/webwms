<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User\UserGroup;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserGroup;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserGroupDataHandler'
)]
readonly class UserGroupDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
    ) {
    }

    public function save(UserGroup $userGroup): void
    {
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();
    }

    public function delete(UserGroup $userGroup): void
    {
        $this->entityManager->remove($userGroup);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, UserGroup>
     */
    public function getAllUserGroups(): array
    {
        return $this->entityManager
            ->getRepository(UserGroup::class)
            ->findAll();
    }

    public function getUserGroupById(int $userGroupId): ?UserGroup
    {
        return $this->entityManager
            ->getRepository(UserGroup::class)
            ->findOneBy(['id' => $userGroupId]);
    }

    public function getUserGroupByUserGroupName(string $userGroupName): ?UserGroup
    {
        return $this->entityManager
            ->getRepository(UserGroup::class)
            ->findOneBy(['group' => $userGroupName]);
    }

    public function addUserGroup(Request $request): ?UserGroup
    {
        $addUserGroup = $request->request->getIterator()->getArrayCopy();
        $userGroup = new UserGroup();

        $userGroup->setGroup($addUserGroup['group']);
        $userGroup->setDescription($addUserGroup['description']);
        $userGroup->setRoles($addUserGroup['roles']);
        $userGroup->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($userGroup);

        return $userGroup;
    }

    public function updateUserGroup(Request $request): ?UserGroup
    {
        $requestData = $request->request->all()['edit_user_group'];
        /** @var UserGroup|null $userGroup */
        $userGroup = $this->entityManager
            ->getRepository(UserGroup::class)
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

        if ($userGroup instanceof UserGroup) {
            $this->delete($userGroup);
        }
    }
}
