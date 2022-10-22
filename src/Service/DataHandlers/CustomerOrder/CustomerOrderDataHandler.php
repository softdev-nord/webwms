<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\CustomerOrder;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\CustomerOrder;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderDataHandler
 */
class CustomerOrderDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(CustomerOrder $customerOrder): void
    {
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
    }

    public function update(CustomerOrder $customerOrder): void
    {
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
    }

    public function delete(CustomerOrder $customerOrder): void
    {
        $this->entityManager->remove($customerOrder);
        $this->entityManager->flush();
    }

    /**
     * @return CustomerOrder|null Returns an array of Customer order objects
     */
    public function getCustomerOrderById(int $customerOrderId): ?CustomerOrder
    {
        return $this->entityManager
            ->getRepository(CustomerOrder::class)
            ->find($customerOrderId);
    }
}
