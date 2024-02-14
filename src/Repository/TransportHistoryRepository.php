<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\TransportHistoryEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryRepository
 *
 * @method TransportHistoryEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportHistoryEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportHistoryEntity[]    findAll()
 * @method TransportHistoryEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, TransportHistoryEntity::class);
    }
}
