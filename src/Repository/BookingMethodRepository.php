<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\BookingMethod;

/**
 * @method BookingMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingMethod[]    findAll()
 * @method BookingMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingMethod::class);
    }

    // /**
    //  * @return BookingMethod[] Returns an array of BookingMethod objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookingMethod
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
