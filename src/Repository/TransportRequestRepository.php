<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\TransportRequestEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestRepository
 *
 * @method TransportRequestEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportRequestEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportRequestEntity[]    findAll()
 * @method TransportRequestEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, TransportRequestEntity::class);
    }
}
