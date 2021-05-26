<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockZone;

/**
 * @method StockZone|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockZone|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockZone[]    findAll()
 * @method StockZone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockZone::class);
    }

    // /**
    //  * @return StockZone[] Returns an array of StockZone objects
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
    public function findOneBySomeField($value): ?StockZone
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
