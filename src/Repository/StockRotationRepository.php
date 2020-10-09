<?php

namespace WebWMS\Repository;

use WebWMS\Entity\StockRotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StockRotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockRotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockRotation[]    findAll()
 * @method StockRotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockRotation::class);
    }

    // /**
    //  * @return StockRotation[] Returns an array of StockRotation objects
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
    public function findOneBySomeField($value): ?StockRotation
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
