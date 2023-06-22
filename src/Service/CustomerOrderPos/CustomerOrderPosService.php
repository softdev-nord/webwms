<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrderPos;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Service\DataHandlers\CustomerOrderPos\CustomerOrderPosDataHandler;

/**
 * @package:    WebWMS\Service\CustomerOrderPos
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosService
 */
class CustomerOrderPosService
{
    public function __construct(
        private readonly CustomerOrderPosDataHandler $customerOrderPosDataHandler
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

    public function addCustomerOrderPos(CustomerOrderPos $supplierOrderPos): void
    {
        $this->customerOrderPosDataHandler->addCustomerOrderPos($supplierOrderPos);
    }

    public function updateCustomerOrderPos(CustomerOrderPos $supplierOrderPos): void
    {
        $this->customerOrderPosDataHandler->updateCustomerOrderPos($supplierOrderPos);
    }

    public function deleteCustomerOrderPos(?CustomerOrderPos $supplierOrderPos): void
    {
        $this->customerOrderPosDataHandler->deleteCustomerOrderPos($supplierOrderPos);
    }
}
