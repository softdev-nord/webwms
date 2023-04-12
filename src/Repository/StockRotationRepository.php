<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockRotation;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationRepository
 *
 * @method StockRotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockRotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockRotation[]    findAll()
 * @method StockRotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockRotation::class);
    }
}
