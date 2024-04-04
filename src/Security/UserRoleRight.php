<?php

declare(strict_types=1);

namespace WebWMS\Security;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\UserEntity;
use WebWMS\Entity\UserGroupEntity;
use WebWMS\Entity\UserRightEntity;
use WebWMS\Entity\UserRoleEntity;

/**
 * @package:    WebWMS\Security
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleRight
 */
class UserRoleRight
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {
    }

    /**
     * Prüft, ob der Benutzer die userRight hat.
     */
    public function hasUserRight(string $userRight): bool
    {
        $user = $this->getCurrentUser();

        if ($user instanceof UserInterface) {
            /** @var UserRoleEntity $role */
            foreach ($user->getRoles() as $role) {
                foreach ($role->getUserRights() as $right) {
                    /** @var UserRightEntity $right */
                    if ($userRight === $right->getUserRight()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Prüft, ob der Benutzer die userRole hat
     */
    public function hasUserRole(string $userRole): bool
    {
        $user = $this->getCurrentUser();

        return $user instanceof UserInterface && in_array($userRole, $user->getRoles(), true);
    }

    /**
     * Prüft, ob der Benutzer die userGroupName hat
     */
    public function hasUserGroup(string $userGroupName): bool
    {
        /** @var UserEntity $user */
        $user = $this->getCurrentUser();
        $userGroupEntity = $this->getUserGroupByGroup($userGroupName);

        return in_array((string) $userGroupEntity->getId(), $user->getUserGroups(), true);
    }

    /**
     * Ruft UserRightEntity mit der ID des Rechts ab. z. Bsp. 1.
     * @return array|UserRightEntity[]
     */
    public function getUserRightById(string $userRightId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRightEntity::class, 'ur')
            ->where('ur.id = :userRightId')
            ->setParameter('userRightId', $userRightId)
            ->setMaxResults(1)
        ;

        $result = $queryBuilder->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ruft UserRightEntity mit dem Namen des Rechts auf. z. Bsp. manage-users.
     * @return array|UserRightEntity[]
     */
    public function getUserRightByRightName(string $userRight): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRightEntity::class, 'ur')
            ->where('ur.userRight = :userRight')
            ->setParameter('userRight', $userRight)
            ->setMaxResults(1)
        ;

        $result = $queryBuilder->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ruft UserRoleEntity mit der ID der Rolle ab. z. Bsp. 1.
     * @return array|UserRoleEntity[]
     */
    public function getUserRoleById(int $userRoleId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.id = :userRoleId')
            ->setParameter('userRoleId', $userRoleId)
            ->setMaxResults(1)
        ;

        $result = $queryBuilder->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ruft UserRoleEntity mit dem Namen der Rolle. z. Bsp. SUPER_ADMIN.
     * @return array|UserRoleEntity[]
     */
    public function getUserRoleByRoleName(string $userRole): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ev.userRole = :userRole')
            ->setParameter('userRole', $userRole)
            ->setMaxResults(1)
        ;

        $result = $queryBuilder->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ruft Group mit der ID der Gruppe ab. z. Bsp. 1.
     * @return array|UserGroupEntity[]
     */
    public function getUserGroupById(int $userGroupId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ug')
            ->from(UserGroupEntity::class, 'ug')
            ->where('ug.id = :userGroupId')
            ->setParameter('userGroupId', $userGroupId)
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ruft Group mit dem Namen der Gruppe. z. Bsp. GROUP_SUPER_ADMIN.
     */
    public function getUserGroupByGroup(string $userGroupName): UserGroupEntity
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ug')
            ->from(UserGroupEntity::class, 'ug')
            ->where('ug.group = :userGroupName')
            ->setParameter('userGroupName', $userGroupName)
            ->setMaxResults(1)
        ;

        $result = $queryBuilder->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Angemeldeten Benutzer abrufen
     */
    public function getCurrentUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * Get the user groups from current user
     * @return array<array<string>>
     */
    protected function getUserGroup(): array
    {
        /** @var UserEntity $user */
        $user = $this->getCurrentUser();

        return $user->getUserGroups();
    }
}
