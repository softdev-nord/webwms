<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\Role;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        RoleService
 */
class RoleService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getAllRoles(): ?array
    {
        return $this->entityManager
            ->getRepository(Role::class)
            ->findAll();
    }
}
