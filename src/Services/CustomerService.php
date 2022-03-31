<?php

declare(strict_types=1);

namespace WebWMS\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\Customer as Customers;

/**
 * @package:    WebWMS\Services
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerService
 */
class CustomerService
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getAllCustomers(): array
    {
        $customers = $this->doctrine->getRepository(Customers::class)->findAll();

        if (!$customers) {
            throw $this->createNotFoundException('Keine Kunden gefunden');
        }

        return $customers;
    }

    public function getAllCustomersAjax(): JsonResponse
    {
        return $this->getCustomers();
    }

    /**
     * Get all Customers for Ajax-Request.
     */
    public function getCustomers(): JsonResponse
    {
        $connection = $this->doctrine->getConnection();

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

        $customer = new Customers();
        $entityManager = $this->doctrine->getManager();
        $customer->setCustomerId($lastCustomer['customer_id'] +1);
        $customer->setCustomerNr($lastCustomer['customer_nr'] +1);
        $customer->setCustomerName($params['customer_name']);
        $customer->setCustomerAddressAddition($params['customer_address_addition']);
        $customer->setCustomerAddressStreet($params['customer_address_street']);
        $customer->setCustomerAddressStreetNr($params['customer_address_street_nr']);
        $customer->setCustomerCountryCode($params['customer_country_code']);
        $customer->setCustomerZipCode($params['customer_zip_code']);
        $customer->setCustomerCity($params['customer_city']);
        $entityManager->persist($customer);
        $entityManager->flush();
    }

    /**
     * Get last customer
     */
    public function getLastCustomer(): array
    {
        $customerOrderRepository = $this->doctrine
            ->getRepository(Customers::class);

        return $customerOrderRepository->findBy([], ['customer_nr' => 'DESC'], 1, 0);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}