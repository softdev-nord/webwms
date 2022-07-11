<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\CustomerOrder;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderRepository
 *
 * @method CustomerOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method []                 findAll()
 * @method CustomerOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerOrderRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($registry, CustomerOrder::class);
    }

    /**
     * @return CustomerOrder
     */
    public function findById(int $customerOrderId): ?CustomerOrder
    {
        return self::find($customerOrderId);
    }

    public function save(CustomerOrder $customerOrder): void
    {
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
    }

    public function delete(CustomerOrder $customerOrder): void
    {
        $this->entityManager->remove($customerOrder);
        $this->entityManager->flush();
    }
}
