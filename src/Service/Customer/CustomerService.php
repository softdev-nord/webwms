<?php

declare(strict_types=1);

namespace WebWMS\Service\Customer;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Customer;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerService
 */
class CustomerService
{
    public function __construct(
        private CustomerDataHandler $customerDataHandler
    ) {
    }

    public function getCustomerById(int $customerId): Customer
    {
        return $this->customerDataHandler->getCustomerById($customerId);
    }

    public function getCustomerByNr(int $customerNr): ?Customer
    {
        return $this->customerDataHandler->getCustomerByNr($customerNr);
    }

    public function getAllCustomers(): JsonResponse
    {
        return $this->customerDataHandler->getAllCustomers();
    }

    public function getAllCustomersAjax(): JsonResponse
    {
        return $this->customerDataHandler->getCustomers();
    }

    public function addCustomer($requestData): void
    {
        $this->customerDataHandler->addCustomer($requestData);
    }

    public function getLastCustomer(): array
    {
        return $this->customerDataHandler->getLastCustomer();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getCustomerApi(int $customerId): ?Customer
    {
        $customer = $this->customerDataHandler->getCustomerById($customerId);

        if (!$customer) {
            throw new EntityNotFoundException('Customer with id '.$customerId.' does not exist!');
        }

        return $customer;
    }

    public function getAllCustomersApi(): ?array
    {
        return $this->customerDataHandler->getAllCustomersApi();
    }

    public function addCustomerApi(Request $request): Customer
    {
        $requestData = $request->request->all();

        return $this->customerDataHandler->addCustomer($requestData);
    }

    public function updateCustomerApi(Request $request): ?Customer
    {
        $requestData = $request->request->all();

        return $this->customerDataHandler->updateCustomer($requestData);
    }

    public function deleteCustomerApi(int $customerId): void
    {
        $this->customerDataHandler->deleteCustomerApi($customerId);
    }

    public function updateCustomer($requestData): ?Customer
    {
        return $this->customerDataHandler->updateCustomer($requestData);
    }
}
