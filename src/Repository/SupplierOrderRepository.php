<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\SupplierOrder;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderRepository
 *
 * @method SupplierOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplierOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplierOrder[]    findAll()
 * @method SupplierOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierOrderRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($registry, SupplierOrder::class);
    }

    /**
     * @return SupplierOrder
     */
    public function findById(int $orderId): ?SupplierOrder
    {
        return self::find($orderId);
    }

    public function save(SupplierOrder $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function delete(SupplierOrder $order): void
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }
}
