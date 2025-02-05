<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrderPos;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\CustomerOrderPos\CustomerOrderPosDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\CustomerOrderPos',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'CustomerOrderPosService'
)]
readonly class CustomerOrderPosService
{
    public function __construct(
        private CustomerOrderPosDataHandler $customerOrderPosDataHandler,
    ) {
    }

    public function getCustomerOrderPosById(int $customerOrderPosId): ?CustomerOrderPos
    {
        return $this->customerOrderPosDataHandler->getCustomerOrderPosById($customerOrderPosId);
    }

    public function getCustomerOrderPosByCustomerOrderId(int $customerOrderId): ?CustomerOrderPos
    {
        return $this->customerOrderPosDataHandler->getCustomerOrderPosByCustomerOrderId($customerOrderId);
    }

    public function getAllCustomerOrderPos(): JsonResponse
    {
        return $this->customerOrderPosDataHandler->getAllCustomerOrderPos();
    }

    public function addCustomerOrderPos(CustomerOrderPos $customerOrderPos): void
    {
        $this->customerOrderPosDataHandler->addCustomerOrderPos($customerOrderPos);
    }

    public function updateCustomerOrderPos(CustomerOrderPos $customerOrderPos): void
    {
        $this->customerOrderPosDataHandler->updateCustomerOrderPos($customerOrderPos);
    }

    public function deleteCustomerOrderPos(?CustomerOrderPos $customerOrderPos): void
    {
        $this->customerOrderPosDataHandler->deleteCustomerOrderPos($customerOrderPos);
    }
}
