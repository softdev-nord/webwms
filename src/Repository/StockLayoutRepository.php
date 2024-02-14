<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockLayoutEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutRepository
 *
 * @method StockLayoutEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLayoutEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLayoutEntity[]    findAll()
 * @method StockLayoutEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, StockLayoutEntity::class);
    }
}
