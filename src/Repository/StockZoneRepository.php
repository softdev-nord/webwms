<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockZone;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockZoneRepository
 *
 * @method StockZone|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockZone|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockZone[]    findAll()
 * @method StockZone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockZone::class);
    }
}
