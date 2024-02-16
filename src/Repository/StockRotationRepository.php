<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\StockRotationEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationRepository
 *
 * @method StockRotationEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockRotationEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockRotationEntity[]    findAll()
 * @method StockRotationEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, StockRotationEntity::class);
    }
}
