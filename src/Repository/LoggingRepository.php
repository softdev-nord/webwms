<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\Logging;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        LoggingRepository
 *
 * @extends ServiceEntityRepository<Logging>
 *
 * @method Logging|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logging|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logging[]    findAll()
 * @method Logging[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoggingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logging::class);
    }
}
