<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Customer;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Customer;

/**
 * @package:    WebWMS\Service\DataHandlers\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerDataHandler
 */
class CustomerDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
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
            ->findOneBy(['customer_nr' => $customerNr]);
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

        $numOfBoxCustomer = !empty($_GET['numOfBoxCustomer']) ? $_GET['numOfBoxCustomer'] : '';
        $nameKd = !empty($_GET['customer_nr']) ? strtolower(trim($_GET['customer_nr'])) : '';

        $boxName = 'customer_nr';

        switch ($numOfBoxCustomer) {
            case 1:
                $boxName = 'customer_name';
                break;
            case 2:
                $boxName = 'customer_address_addition';
                break;
            case 3:
                $boxName = 'customer_address_street';
                break;
            case 4:
                $boxName = 'customer_address_street_nr';
                break;
            case 5:
                $boxName = 'customer_country_code';
                break;
            case 6:
                $boxName = 'customer_zip_code';
                break;
            case 7:
                $boxName = 'customer_city';
                break;
            case 8:
                $boxName = 'id';
                break;
        }

        $data = [];
        if (isset($_GET['name_customer'])) {
            $nameKd = strtolower(trim($_GET['name_customer']));

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

    public function addNewCustomer(Request $request)
    {
        $params = $request->request->all()['customer'];
        $lastCustomer = $this->getLastCustomer()[0]->toArray();

        $customer = new Customer();

        $customer->setCustomerId($lastCustomer['customer_id'] + 1);
        $customer->setCustomerNr($lastCustomer['customer_nr'] + 1);
        $customer->setCustomerName($params['customer_name']);
        $customer->setCustomerAddressAddition($params['customer_address_addition']);
        $customer->setCustomerAddressStreet($params['customer_address_street']);
        $customer->setCustomerAddressStreetNr($params['customer_address_street_nr']);
        $customer->setCustomerCountryCode($params['customer_country_code']);
        $customer->setCustomerZipCode($params['customer_zip_code']);
        $customer->setCustomerCity($params['customer_city']);

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
            ->getRepository(Customer::class)->findById($customerMainId);

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
            ->getRepository(Customer::class)->findBy([], ['customer_nr' => 'DESC'], 1, 0);
    }

    public function updateCustomer($requestData): ?Customer
    {
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));
        $customer = $this->entityManager
            ->getRepository(Customer::class)
            ->findOneBy(['customer_nr' => $requestData['customer_nr']]);

        if (!$customer) {
            return null;
        }

        $customer->setCustomerId((int) $requestData['customer_id']);
        $customer->setCustomerNr((int) $requestData['customer_nr']);
        $customer->setCustomerName((string) $requestData['customer_name']);
        $customer->setCustomerAddressAddition((string) $requestData['customer_address_addition']);
        $customer->setCustomerAddressStreet((string) $requestData['customer_address_street']);
        $customer->setCustomerAddressStreetNr((string) $requestData['customer_address_street_nr']);
        $customer->setCustomerCountryCode((string) $requestData['customer_country_code']);
        $customer->setCustomerZipCode((string) $requestData['customer_zip_code']);
        $customer->setCustomerCity((string) $requestData['customer_city']);
        $customer->setCustomerUpdatedAt($updatedAt);

        $this->update($customer);

        return $customer;
    }
}
