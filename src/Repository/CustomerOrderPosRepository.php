<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\CustomerOrderPosEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosRepository
 *
 * @method CustomerOrderPosEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerOrderPosEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerOrderPosEntity[]    findAll()
 * @method CustomerOrderPosEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerOrderPosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, CustomerOrderPosEntity::class);
    }
}
