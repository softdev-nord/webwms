<?php

declare(strict_types=1);

namespace WebWMS\Service\Customer;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'CustomerService'
)]
class CustomerService
{
    public function __construct(
        private readonly CustomerDataHandler $customerDataHandler,
    ) {
    }

    public function getCustomerById(int $customerId): ?Customer
    {
        return $this->customerDataHandler->getCustomerById($customerId);
    }

    public function getCustomerByNr(int $customerNr): ?Customer
    {
        return $this->customerDataHandler->getCustomerByNr($customerNr);
    }

    public function getAllCustomers(): JsonResponse
    {
        return new JsonResponse($this->customerDataHandler->getAllCustomers());
    }

    public function getAllCustomersAjax(?string $customerNrInput): JsonResponse
    {
        return $this->customerDataHandler->getCustomers($customerNrInput);
    }

    public function addCustomer(Customer $customer): void
    {
        $this->customerDataHandler->addCustomer($customer);
    }

    public function updateCustomer(Customer $customer): void
    {
        $this->customerDataHandler->updateCustomer($customer);
    }

    public function deleteCustomer(Customer $customer): void
    {
        $this->customerDataHandler->deleteCustomer($customer);
    }

    public function getLastCustomer(): Customer
    {
        return $this->customerDataHandler->getLastCustomer();
    }
}
