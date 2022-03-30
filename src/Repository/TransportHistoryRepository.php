<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\TransportHistory;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        TransportHistoryRepository
 *
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
}
