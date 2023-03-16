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
 * @copyright:  Copyright © 2022, SoftDev Nord
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

    /**
     * @return Customer|null Returns an array of Customer objects
     */
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

    public function getAllCustomers(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('customer');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    /**
     * Get all Customers for Ajax-Request.
     */
    public function getCustomers(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();
        $numOfBoxCustomer = filter_input(INPUT_GET, 'numOfBoxCustomer') !== null ? filter_input(INPUT_GET, 'numOfBoxCustomer') : '';

        $boxName = match ($numOfBoxCustomer) {
            'customer_name' => 'customer_name',
            'customer_address_addition' => 'customer_address_addition',
            'customer_address_street' => 'customer_address_street',
            'customer_address_street_nr' => 'customer_address_street_nr',
            'customer_country_code' => 'customer_country_code',
            'customer_zip_code' => 'customer_zip_code',
            'customer_city' => 'customer_city',
            default => 'customer_nr',
        };

        $data = [];
        if (filter_input(INPUT_GET, 'name_customer') !== null) {
            $nameCustomer = strtolower(trim(strval(filter_input(INPUT_GET, 'name_customer'))));

            $sqlKd = "SELECT customer_nr, customer_name, customer_address_addition, 
                        customer_address_street, customer_address_street_nr, customer_country_code, 
                        customer_zip_code, customer_city, customer_id FROM customer WHERE LOWER($boxName) LIKE '" . $nameCustomer . "%'";
            $stmt = $connection->executeQuery($sqlKd);

            while ($rowCustomer = $stmt->fetchAssociative()) {
                $nameCustomer = $rowCustomer['customer_nr'] . '|' .
                    $rowCustomer['customer_name'] . '|' .
                    $rowCustomer['customer_address_addition'] . '|' .
                    $rowCustomer['customer_address_street'] . '|' .
                    $rowCustomer['customer_address_street_nr'] . '|' .
                    $rowCustomer['customer_country_code'] . '|' .
                    $rowCustomer['customer_zip_code'] . '|' .
                    $rowCustomer['customer_city'] . '|' .
                    $rowCustomer['customer_id']
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

    /**
     * @return object[]
     */
    public function getLastCustomer(): array
    {
        return $this->entityManager
            ->getRepository(Customer::class)->findBy([], ['customerNr' => 'DESC'], 1, 0);
    }
}
