<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\SupplierOrder;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderRepository
 *
 * @method SupplierOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplierOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplierOrder[]    findAll()
 * @method SupplierOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierOrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, SupplierOrder::class);
    }
}
