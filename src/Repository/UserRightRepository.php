<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\UserRight;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRightRepository
 *
 * @method UserRight|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRight|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRight[]    findAll()
 * @method UserRight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRight::class);
    }
}
