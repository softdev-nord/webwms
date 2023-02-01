<?php

declare(strict_types=1);

namespace WebWMS\Service\Customer;

use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function updateCustomer($requestData): ?Customer
    {
        return $this->customerDataHandler->updateCustomer($requestData);
    }
}
