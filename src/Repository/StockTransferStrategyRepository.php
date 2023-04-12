<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockTransferStrategy;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockTransferStrategyRepository
 *
 * @method StockTransferStrategy|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockTransferStrategy|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockTransferStrategy[]    findAll()
 * @method StockTransferStrategy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockTransferStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockTransferStrategy::class);
    }
}
