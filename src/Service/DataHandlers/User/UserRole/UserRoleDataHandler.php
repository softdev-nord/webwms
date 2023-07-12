<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\User\UserRole;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRole;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Role
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleDataHandler
 */
class UserRoleDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(UserRole $userRole): void
    {
        $this->entityManager->persist($userRole);
        $this->entityManager->flush();
    }

    public function delete(UserRole $userRole): void
    {
        $this->entityManager->remove($userRole);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, UserRole>
     */
    public function getAllUserRoles(): array
    {
        return $this->entityManager
            ->getRepository(UserRole::class)
            ->findAll();
    }

    public function getUserRoleById(int $userRoleId): ?UserRole
    {
        return $this->entityManager
            ->getRepository(UserRole::class)
            ->findOneBy(['id' => $userRoleId]);
    }

    public function getUserRoleByUserRoleName(string $userRoleName): ?UserRole
    {
        return $this->entityManager
            ->getRepository(UserRole::class)
            ->findOneBy(['user_role' => $userRoleName]);
    }

    public function addUserRole(Request $request): ?UserRole
    {
        $addUserRole = $request->request->getIterator()->getArrayCopy();
        $userRole = new UserRole();

        $userRole->setUserRole($addUserRole['user_role']);
        $userRole->setDescription($addUserRole['description']);
        $userRole->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($userRole);

        return $userRole;
    }

    public function updateUserRole(Request $request): ?UserRole
    {
        $requestData = $request->request->all()['edit_user_role'];
        $userRole = $this->entityManager
            ->getRepository(UserRole::class)
            ->findOneBy(['user_role' => $requestData['user_role']]);

        if ($userRole === null) {
            return null;
        }

        $userRole->setUserRole($requestData['user_role']);
        $userRole->setDescription($requestData['description']);
        $userRole->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($userRole);

        return $userRole;
    }

    public function deleteUserRole(string $userRoleName): void
    {
        $userRole = $this->getUserRoleByUserRoleName($userRoleName);

        if ($userRole !== null) {
            $this->delete($userRole);
        }
    }
}
