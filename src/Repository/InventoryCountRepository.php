<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\Inventory;
use WebWMS\Entity\InventoryCount;

class InventoryCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, InventoryCount::class);
    }

    public function save(InventoryCount $count): void
    {
        $this->getEntityManager()->persist($count);
        $this->getEntityManager()->flush();
    }

    /**
     * @return InventoryCount[]
     */
    public function findByInventory(Inventory $inventory): array
    {
        return $this->findBy(['inventory' => $inventory]);
    }

    /**
     * @return InventoryCount[]
     */
    public function findUncountedByInventory(Inventory $inventory): array
    {
        return $this->createQueryBuilder('ic')
            ->where('ic.inventory = :inventory')
            ->andWhere('ic.countedQuantity IS NULL')
            ->setParameter('inventory', $inventory)
            ->getQuery()
            ->getResult();
    }
}

