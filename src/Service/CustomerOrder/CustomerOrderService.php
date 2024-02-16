<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrder;

use Symfony\Component\HttpFoundation\JsonResponse;

use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

/**
 * @package:    WebWMS\Service\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderService
 */
class CustomerOrderService
{
    public function __construct(
        private readonly CustomerOrderDataHandler $customerOrderDataHandler
    ) {
    }

    public function getCustomerOrderById(int $id): ?CustomerOrderEntity
    {
        return $this->customerOrderDataHandler->getCustomerOrderById($id);
    }

    /**
     * Get all CustomerEntity Orders for Ajax-Request.
     */
    public function getAllCustomerOrders(): JsonResponse
    {
        return $this->customerOrderDataHandler->getAllCustomerOrders();
    }

    /**
     * Get all CustomerEntity Order Pos for Ajax-Request.
     */
    public function getAllCustomerOrderPos(): JsonResponse
    {
        return $this->customerOrderDataHandler->getAllCustomerOrderPos();
    }

    public function getCustomerOrderPosByOrderId(int $id): JsonResponse
    {
        return $this->customerOrderDataHandler->getCustomerOrderPosByCustomerOrderId($id);
    }

    /**
     * @return object[]
     */
    public function getLastCustomerOrderId(): array
    {
        return $this->customerOrderDataHandler->getLastCustomerOrderId();
    }

    public function addCustomerOrder(CustomerOrderEntity $customerOrderEntity): void
    {
        $this->customerOrderDataHandler->addCustomerOrder($customerOrderEntity);
    }

    public function updateCustomerOrder(CustomerOrderEntity $customerOrderEntity): void
    {
        $this->customerOrderDataHandler->updateCustomerOrder($customerOrderEntity);
    }

    public function deleteCustomerOrder(CustomerOrderEntity $customerOrderEntity): void
    {
        $this->customerOrderDataHandler->deleteCustomerOrder($customerOrderEntity);
    }
}
