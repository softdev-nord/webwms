<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationRepository
 *
 * @method StockLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLocation[]    findAll()
 * @method StockLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLocationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, StockLocation::class);
    }
}
