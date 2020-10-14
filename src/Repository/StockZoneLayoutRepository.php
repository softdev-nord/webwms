<?php

namespace WebWMS\Repository;

use WebWMS\Entity\StockZoneLayout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StockZoneLayout|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockZoneLayout|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockZoneLayout[]    findAll()
 * @method StockZoneLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockZoneLayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockZoneLayout::class);
    }

    // /**
    //  * @return StockZoneLayout[] Returns an array of StockZoneLayout objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StockZoneLayout
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
