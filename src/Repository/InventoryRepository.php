<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\Inventory;

class InventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Inventory::class);
    }

    public function save(Inventory $inventory): void
    {
        $this->getEntityManager()->persist($inventory);
        $this->getEntityManager()->flush();
    }

    public function getNextInventoryNr(): string
    {
        $last = $this->createQueryBuilder('i')
            ->select('i.inventoryNr')
            ->orderBy('i.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$last) {
            return 'INV-0001';
        }

        $lastNr = (int) substr($last['inventoryNr'], 4);
        return 'INV-' . str_pad((string) ($lastNr + 1), 4, '0', STR_PAD_LEFT);
    }
}

