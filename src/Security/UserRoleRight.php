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
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Security',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserRoleRight'
)]
readonly class UserRoleRight
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
    ) {
    }

    /**
     * Prüft, ob der Benutzer die userRight hat.
     */
    public function hasUserRight(string $userRight): bool
    {
        $user = $this->getCurrentUser();

        if ($user instanceof UserInterface) {
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
     */
    public function hasUserRole(string $userRole): bool
    {
        $user = $this->getCurrentUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return in_array($userRole, $user->getRoles(), true);
    }

    /**
     * Prüft, ob der Benutzer die userGroupName hat
     */
    public function hasUserGroup(string $userGroupName): bool
    {
        /** @var User $user */
        $user = $this->getCurrentUser();
        $userGroup = $this->getUserGroupByGroup($userGroupName);

        return in_array((string) ($userGroup->getId()), $user->getUserGroups(), true);
    }

    /**
     * Ruft UserRight mit der ID des Rechts ab. z. Bsp. 1.
     * @return array|UserRight[]
     */
    public function getUserRightById(string $userRightId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRight::class, 'ur')
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
     * Ruft UserRight mit dem Namen des Rechts auf. z. Bsp. manage-users.
     * @return array|UserRight[]
     */
    public function getUserRightByRightName(string $userRight): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRight::class, 'ur')
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
     * Ruft UserRole mit der ID der Rolle ab. z. Bsp. 1.
     * @return array|UserRole[]
     */
    public function getUserRoleById(int $userRoleId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRole::class, 'ur')
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
     * Ruft UserRole mit dem Namen der Rolle. z. Bsp. SUPER_ADMIN.
     * @return array|UserRole[]
     */
    public function getUserRoleByRoleName(string $userRole): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ur')
            ->from(UserRole::class, 'ur')
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
     * @return array|UserGroup[]
     */
    public function getUserGroupById(int $userGroupId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ug')
            ->from(UserGroup::class, 'ug')
            ->where('ug.id = :userGroupId')
            ->setParameter('userGroupId', $userGroupId)
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ruft Group mit dem Namen der Gruppe. z. Bsp. GROUP_SUPER_ADMIN.
     */
    public function getUserGroupByGroup(string $userGroupName): UserGroup
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('ug')
            ->from(UserGroup::class, 'ug')
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
     * @return array<string>
     */
    protected function getUserGroup(): array
    {
        /** @var User $user */
        $user = $this->getCurrentUser();

        return $user->getUserGroups();
    }
}
