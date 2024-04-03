<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerDataHandler
 */
class CustomerDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(CustomerEntity $customerEntity): void
    {
        $this->entityManager->persist($customerEntity);
        $this->entityManager->flush();
    }

    public function delete(CustomerEntity $customerEntity): void
    {
        $this->entityManager->remove($customerEntity);
        $this->entityManager->flush();
    }

    public function getCustomerById(int $customerId): ?CustomerEntity
    {
        return $this->entityManager
            ->getRepository(CustomerEntity::class)
            ->find($customerId);
    }

    public function getCustomerByNr(int $customerNr): ?CustomerEntity
    {
        return $this->entityManager
            ->getRepository(CustomerEntity::class)
            ->findOneBy(['customerNr' => $customerNr]);
    }

    /**
     * @return array<mixed>
     */
    public function getAllCustomers(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('c')
            ->from(CustomerEntity::class, 'c')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Get all Customers for Ajax-Request.
     */
    public function getCustomers(?string $customerNrInput): JsonResponse
    {
        $data = [];
        if ($customerNrInput !== null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('c')
                ->from(CustomerEntity::class, 'c')
                ->where('c.customerNr LIKE :customer_nr')
                ->setParameter(':customer_nr', '%' . $customerNrInput . '%');

            $customers = $queryBuilder->getQuery()->getArrayResult();

            foreach ($customers as $customer) {
                $nameCustomer = $customer['customerId'] . ' | ' .
                    $customer['customerNr'] . ' | ' .
                    $customer['customerName'] . ' | ' .
                    $customer['customerAddressAddition'] . ' | ' .
                    $customer['customerAddressStreet'] . ' | ' .
                    $customer['customerAddressStreetNr'] . ' | ' .
                    $customer['customerCountryCode'] . ' | ' .
                    $customer['customerZipCode'] . ' | ' .
                    $customer['customerCity'] . ' | ' .
                    $customer['customerId']
                ;
                $data[] = $nameCustomer;
            }
        }

        return new JsonResponse($data);
    }

    public function addCustomer(CustomerEntity $customerEntity): void
    {
        $customerEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($customerEntity);
    }

    public function updateCustomer(CustomerEntity $customerEntity): void
    {
        $customerEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($customerEntity);
    }

    public function deleteCustomer(CustomerEntity $customerEntity): void
    {
        $this->delete($customerEntity);
    }

    public function getLastCustomer(): CustomerEntity
    {
        $lastCustomer = $this->entityManager
            ->getRepository(CustomerEntity::class)
            ->findBy([], ['customerId' => 'DESC'], 1, 0);

        return $lastCustomer[0];
    }
}
