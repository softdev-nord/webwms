<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockOccupancy;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockOccupancyRepository
 *
 * @extends ServiceEntityRepository<StockOccupancy>
 *
 * @method StockOccupancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockOccupancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockOccupancy[]    findAll()
 * @method StockOccupancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockOccupancyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockOccupancy::class);
    }
}
