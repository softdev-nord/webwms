<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockLayout;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutRepository
 *
 * @method StockLayout|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLayout|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLayout[]    findAll()
 * @method StockLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockLayout::class);
    }
}
