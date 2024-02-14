<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\SupplierEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierRepository
 *
 * @method SupplierEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplierEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplierEntity[]    findAll()
 * @method SupplierEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($managerRegistry, SupplierEntity::class);
    }
}
