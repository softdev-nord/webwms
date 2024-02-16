<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\UserRoleEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleRepository
 *
 * @method UserRoleEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRoleEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRoleEntity[]    findAll()
 * @method UserRoleEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, UserRoleEntity::class);
    }
}
