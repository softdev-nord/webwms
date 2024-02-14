<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\LoggingEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LoggingRepository
 *
 * @extends ServiceEntityRepository<LoggingEntity>
 *
 * @method LoggingEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoggingEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoggingEntity[]    findAll()
 * @method LoggingEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoggingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, LoggingEntity::class);
    }
}
