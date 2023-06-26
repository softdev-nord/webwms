<?php

declare(strict_types=1);

namespace WebWMS\Security;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\User;
use WebWMS\Entity\UserGroup;
use WebWMS\Entity\UserRight;
use WebWMS\Entity\UserRole;

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
     * @param string $userRight
     * @return bool
     */
    public function hasUserRight(string $userRight): bool
    {
        $user = $this->getCurrentUser();

        if ($user !== null) {
            /** @var UserRole $role */
            foreach ($user->getRoles() as $role) {
                foreach ($role->getUserRights() as $right) {
                    /** @var UserRight $right */
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
     * @param string $userRole
     * @return bool
     */
    public function hasUserRole(string $userRole): bool
    {
        $user = $this->getCurrentUser();

        if ($user !== null) {
            if (in_array($userRole, $user->getRoles(), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prüft, ob der Benutzer die userGroupName hat
     * @param string $userGroupName
     * @return bool
     */
    public function hasUserGroup(string $userGroupName): bool
    {
        /** @var User $user */
        $user = $this->getCurrentUser();
        $userGroup = $this->getUserGroupByGroup($userGroupName);

        if (in_array(strval($userGroup->getId()), $user->getUserGroups(), true)) {
            return true;
        }

        return false;
    }

    /**
     * Ruft UserRight mit der ID des Rechts ab. z. Bsp. 1.
     * @param string $userRightId
     * @return array|UserRight[]
     */
    public function getUserRightById(string $userRightId): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('ur')
            ->from(UserRight::class, 'ur')
            ->where('ur.id = :userRightId')
            ->setParameter('userRightId', $userRightId)
            ->setMaxResults(1)
        ;

        $result = $query->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Ruft UserRight mit dem Namen des Rechts auf. z. Bsp. manage-users.
     * @param string $userRight
     * @return array|UserRight[]
     */
    public function getUserRightByRightName(string $userRight): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('ur')
            ->from(UserRight::class, 'ur')
            ->where('ur.userRight = :userRight')
            ->setParameter('userRight', $userRight)
            ->setMaxResults(1)
        ;

        $result = $query->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Ruft UserRole mit der ID der Rolle ab. z. Bsp. 1.
     * @param int $userRoleId
     * @return array|UserRole[]
     */
    public function getUserRoleById(int $userRoleId): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('ur')
            ->from(UserRole::class, 'ur')
            ->where('ur.id = :userRoleId')
            ->setParameter('userRoleId', $userRoleId)
            ->setMaxResults(1)
        ;

        $result = $query->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Ruft UserRole mit dem Namen der Rolle. z. Bsp. SUPER_ADMIN.
     * @param string $userRole
     * @return array|UserRole[]
     */
    public function getUserRoleByRoleName(string $userRole): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('ur')
            ->from(UserRole::class, 'ur')
            ->where('ev.userRole = :userRole')
            ->setParameter('userRole', $userRole)
            ->setMaxResults(1)
        ;

        $result = $query->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Ruft Group mit der ID der Gruppe ab. z. Bsp. 1.
     * @param int $userGroupId
     * @return array|UserGroup[]
     */
    public function getUserGroupById(int $userGroupId): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('ug')
            ->from(UserGroup::class, 'ug')
            ->where('ug.id = :userGroupId')
            ->setParameter('userGroupId', $userGroupId)
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Ruft Group mit dem Namen der Gruppe. z. Bsp. GROUP_SUPER_ADMIN.
     * @param string $userGroupName
     * @return UserGroup
     */
    public function getUserGroupByGroup(string $userGroupName): UserGroup
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('ug')
            ->from(UserGroup::class, 'ug')
            ->where('ug.group = :userGroupName')
            ->setParameter('userGroupName', $userGroupName)
            ->setMaxResults(1)
        ;

        $result = $query->getQuery()->getResult();

        if (isset($result)) {
            return $result[0];
        }

        return $query->getQuery()->getResult();
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
     * @return array<string>
     */
    protected function getUserGroup(): array
    {
        /** @var User $user */
        $user = $this->getCurrentUser();

        return $user->getUserGroups();
    }
}
