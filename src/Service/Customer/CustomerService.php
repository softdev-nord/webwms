<?php

declare(strict_types=1);

namespace WebWMS\Service\Customer;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerService
 */
class CustomerService
{
    public function __construct(
        private readonly CustomerDataHandler $customerDataHandler
    ) {
    }

    public function getCustomerById(int $customerId): ?CustomerEntity
    {
        return $this->customerDataHandler->getCustomerById($customerId);
    }

    public function getCustomerByNr(int $customerNr): ?CustomerEntity
    {
        return $this->customerDataHandler->getCustomerByNr($customerNr);
    }

    public function getAllCustomers(): JsonResponse
    {
        return new JsonResponse($this->customerDataHandler->getAllCustomers());
    }

    public function getAllCustomersAjax(string|null $customerNrInput): JsonResponse
    {
        return $this->customerDataHandler->getCustomers($customerNrInput);
    }

    public function addCustomer(CustomerEntity $customerEntity): void
    {
        $this->customerDataHandler->addCustomer($customerEntity);
    }

    public function updateCustomer(CustomerEntity $customerEntity): void
    {
        $this->customerDataHandler->updateCustomer($customerEntity);
    }

    public function deleteCustomer(CustomerEntity $customerEntity): void
    {
        $this->customerDataHandler->deleteCustomer($customerEntity);
    }

    public function getLastCustomer(): CustomerEntity
    {
        return $this->customerDataHandler->getLastCustomer();
    }
}
