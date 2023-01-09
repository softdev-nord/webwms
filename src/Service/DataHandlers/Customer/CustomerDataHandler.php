<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Customer;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function update(Customer $customer): void
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

    /**
     * @throws Exception
     */
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

        $numOfBoxCustomer = !empty(filter_input(INPUT_GET, 'numOfBoxCustomer')) ? filter_input(INPUT_GET, 'numOfBoxCustomer') : '';
        $nameKd = !empty(filter_input(INPUT_GET, 'customer_nr')) ? strtolower(trim(filter_input(INPUT_GET, 'customer_nr'))) : '';

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
        if (!empty(filter_input(INPUT_GET, 'name_customer'))) {
            $nameKd = strtolower(trim(filter_input(INPUT_GET, 'name_customer')));

            $sqlKd = "SELECT customer_nr, customer_name, customer_address_addition, 
                        customer_address_street, customer_address_street_nr, customer_country_code, 
                        customer_zip_code, customer_city, id FROM customer WHERE LOWER($boxName) LIKE '".$nameKd."%'";
            $stmt = $connection->executeQuery($sqlKd);

            while ($rowKd = $stmt->fetchAssociative()) {
                $nameKd = $rowKd['customer_nr'].'|'.$rowKd['customer_name'].'|'.$rowKd['customer_address_addition'].'|'.$rowKd['customer_address_street'].'|'.$rowKd['customer_address_street_nr'].'|'.$rowKd['customer_country_code'].'|'.$rowKd['customer_zip_code'].'|'.$rowKd['customer_city'].'|'.$rowKd['id'];
                $data[] = $nameKd;
            }
        }

        return new JsonResponse($data);
    }

    public function addCustomer($requestData): void
    {
        $customer = new Customer();

        $customer->setCustomerNr((int) $requestData['customerNr']);
        $customer->setCustomerName((string) $requestData['customerName']);
        $customer->setCustomerAddressAddition((string) $requestData['customerAddressAddition']);
        $customer->setCustomerAddressStreet((string) $requestData['customerAddressStreet']);
        $customer->setCustomerAddressStreetNr((string) $requestData['customerAddressStreetNr']);
        $customer->setCustomerCountryCode((string) $requestData['customerCountryCode']);
        $customer->setCustomerZipCode((string) $requestData['customerZipCode']);
        $customer->setCustomerCity((string) $requestData['customerCity']);
        $customer->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($customer);
    }

    public function getAllCustomersApi(): ?array
    {
        return $this->entityManager
            ->getRepository(Customer::class)->findAll();
    }

    public function addCustomerApi(
        int $customerId,
        int $customerNr,
        string $customerName,
        string $customerAddressAddition,
        string $customerAddressStreet,
        string $customerAddressStreetNr,
        string $customerCountryCode,
        string $customerZipCode,
        string $customerCity
    ): Customer {
        $customer = new Customer();
        $customer->setCustomerId($customerId);
        $customer->setCustomerNr($customerNr);
        $customer->setCustomerName($customerName);
        $customer->setCustomerAddressAddition($customerAddressAddition);
        $customer->setCustomerAddressStreet($customerAddressStreet);
        $customer->setCustomerAddressStreetNr($customerAddressStreetNr);
        $customer->setCustomerCountryCode($customerCountryCode);
        $customer->setCustomerZipCode($customerZipCode);
        $customer->setCustomerCity($customerCity);
        $this->save($customer);

        return $customer;
    }

    public function updateCustomerApi(
        int $customerMainId,
        int $customerId,
        int $customerNr,
        string $customerName,
        string $customerAddressAddition,
        string $customerAddressStreet,
        string $customerAddressStreetNr,
        string $customerCountryCode,
        string $customerZipCode,
        string $customerCity
    ): ?Customer {
        $customer = $this->entityManager
            ->getRepository(Customer::class)->find($customerMainId);

        if (!$customer) {
            return null;
        }

        $customer->setCustomerId($customerId);
        $customer->setCustomerNr($customerNr);
        $customer->setCustomerName($customerName);
        $customer->setCustomerAddressAddition($customerAddressAddition);
        $customer->setCustomerAddressStreet($customerAddressStreet);
        $customer->setCustomerAddressStreetNr($customerAddressStreetNr);
        $customer->setCustomerCountryCode($customerCountryCode);
        $customer->setCustomerZipCode($customerZipCode);
        $customer->setCustomerCity($customerCity);
        $this->save($customer);

        return $customer;
    }

    public function deleteCustomerApi(int $customerId): void
    {
        $customer = $this->getCustomerById($customerId);
        if ($customer) {
            $this->delete($customer);
        }
    }

    public function getLastCustomer(): array
    {
        return $this->entityManager
            ->getRepository(Customer::class)->findBy([], ['customerNr' => 'DESC'], 1, 0);
    }

    public function updateCustomer($requestData): ?Customer
    {
        $customer = $this->entityManager
            ->getRepository(Customer::class)
            ->findOneBy(['customerNr' => $requestData['customerNr']]);

        if (!$customer) {
            return null;
        }

        $customer->setCustomerNr((int) $requestData['customerNr']);
        $customer->setCustomerName((string) $requestData['customerName']);
        $customer->setCustomerAddressAddition((string) $requestData['customerAddressAddition']);
        $customer->setCustomerAddressStreet((string) $requestData['customerAddressStreet']);
        $customer->setCustomerAddressStreetNr((string) $requestData['customerAddressStreetNr']);
        $customer->setCustomerCountryCode((string) $requestData['customerCountryCode']);
        $customer->setCustomerZipCode((string) $requestData['customerZipCode']);
        $customer->setCustomerCity((string) $requestData['customerCity']);
        $customer->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->update($customer);

        return $customer;
    }
}
