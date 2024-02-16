<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockZoneEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneRepository
 *
 * @method StockZoneEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockZoneEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockZoneEntity[]    findAll()
 * @method StockZoneEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, StockZoneEntity::class);
    }
}
