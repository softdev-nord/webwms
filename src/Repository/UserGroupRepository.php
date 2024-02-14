<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\UserGroupEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserGroupRepository
 *
 * @method UserGroupEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroupEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroupEntity[]    findAll()
 * @method UserGroupEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, UserGroupEntity::class);
    }
}
