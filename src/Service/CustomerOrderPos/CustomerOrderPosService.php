<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrderPos;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Service\DataHandlers\CustomerOrderPos\CustomerOrderPosDataHandler;

/**
 * @package:    WebWMS\Service\CustomerOrderPosEntity
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

    public function getCustomerOrderPosById(int $customerOrderPosId): ?CustomerOrderPosEntity
    {
        return $this->customerOrderPosDataHandler->getCustomerOrderPosById($customerOrderPosId);
    }

    public function getCustomerOrderPosByCustomerOrderId(int $customerOrderId): ?CustomerOrderPosEntity
    {
        return $this->customerOrderPosDataHandler->getCustomerOrderPosByCustomerOrderId($customerOrderId);
    }

    public function getAllCustomerOrderPos(): JsonResponse
    {
        return $this->customerOrderPosDataHandler->getAllCustomerOrderPos();
    }

    public function addCustomerOrderPos(CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $this->customerOrderPosDataHandler->addCustomerOrderPos($customerOrderPosEntity);
    }

    public function updateCustomerOrderPos(CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $this->customerOrderPosDataHandler->updateCustomerOrderPos($customerOrderPosEntity);
    }

    public function deleteCustomerOrderPos(?CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $this->customerOrderPosDataHandler->deleteCustomerOrderPos($customerOrderPosEntity);
    }
}
