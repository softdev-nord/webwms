<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\TransportHistory;

/**
 * @method TransportHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportHistory[]    findAll()
 * @method TransportHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportHistory::class);
    }

    // /**
    //  * @return TransportHistory[] Returns an array of TransportHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransportHistory
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
