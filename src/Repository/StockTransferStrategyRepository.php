<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockTransferStrategyEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockTransferStrategyRepository
 *
 * @method StockTransferStrategyEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockTransferStrategyEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockTransferStrategyEntity[]    findAll()
 * @method StockTransferStrategyEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockTransferStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, StockTransferStrategyEntity::class);
    }
}
