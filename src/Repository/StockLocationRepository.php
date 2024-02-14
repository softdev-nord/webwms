<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockLocationEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationRepository
 *
 * @method StockLocationEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLocationEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLocationEntity[]    findAll()
 * @method StockLocationEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLocationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($managerRegistry, StockLocationEntity::class);
    }
}
