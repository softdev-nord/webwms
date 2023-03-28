<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockZoneLayout;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneLayoutRepository
 *
 * @method StockZoneLayout|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockZoneLayout|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockZoneLayout[]    findAll()
 * @method StockZoneLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockZoneLayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockZoneLayout::class);
    }
}
