<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerDataHandler
 */
class CustomerDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(Customer $customer): void
    {
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    public function delete(Customer $customer): void
    {
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }

    public function getCustomerById(int $customerId): ?Customer
    {
        return $this->entityManager
            ->getRepository(Customer::class)
            ->find($customerId);
    }

    public function getCustomerByNr(int $customerNr): ?Customer
    {
        return $this->entityManager
            ->getRepository(Customer::class)
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
            ->from(Customer::class, 'c')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Get all Customers for Ajax-Request.
     */
    public function getCustomers(string|null $customerNrInput): JsonResponse
    {
        $data = [];
        if ($customerNrInput !== null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('c')
                ->from(Customer::class, 'c')
                ->where('c.customerNr LIKE :customer_nr')
                ->setParameter(':customer_nr', '' . $customerNrInput . '%');

            $customers = $queryBuilder->getQuery()->getArrayResult();

            foreach ($customers as $customer) {
                $nameCustomer = $customer['customerNr'] . '|' .
                    $customer['customerName'] . '|' .
                    $customer['customerAddressAddition'] . '|' .
                    $customer['customerAddressStreet'] . '|' .
                    $customer['customerAddressStreetNr'] . '|' .
                    $customer['customerCountryCode'] . '|' .
                    $customer['customerZipCode'] . '|' .
                    $customer['customerCity'] . '|' .
                    $customer['customerId']
                ;
                $data[] = $nameCustomer;
            }
        }

        return new JsonResponse($data);
    }

    public function addCustomer(Customer $customer): void
    {
        $customer->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($customer);
    }

    public function updateCustomer(Customer $customer): void
    {
        $customer->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($customer);
    }

    public function deleteCustomer(Customer $customer): void
    {
        $this->delete($customer);
    }

    public function getLastCustomer(): Customer
    {
        $lastCustomer = $this->entityManager
            ->getRepository(Customer::class)
            ->findBy([], ['customerId' => 'DESC'],1, 0);

        return $lastCustomer[0];
    }
}
