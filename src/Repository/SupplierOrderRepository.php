<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\SupplierOrderEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderRepository
 *
 * @method SupplierOrderEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplierOrderEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplierOrderEntity[]    findAll()
 * @method SupplierOrderEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierOrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($managerRegistry, SupplierOrderEntity::class);
    }
}
