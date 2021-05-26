<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockLayout;

/**
 * @method StockLayout|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLayout|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLayout[]    findAll()
 * @method StockLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockLayout::class);
    }

    // /**
    //  * @return StockLayout[] Returns an array of StockLayout objects
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
    public function findOneBySomeField($value): ?StockLayout
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
