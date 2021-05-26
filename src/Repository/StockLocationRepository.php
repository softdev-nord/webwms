<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockLocation;

/**
 * @method StockLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLocation[]    findAll()
 * @method StockLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockLocation::class);
    }
}
