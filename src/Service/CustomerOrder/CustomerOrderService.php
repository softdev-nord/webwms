<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrder;

use Symfony\Component\HttpFoundation\JsonResponse;

use WebWMS\Entity\CustomerOrder;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

/**
 * @package:    WebWMS\Service\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderService
 */
class CustomerOrderService
{
    public function __construct(
        private CustomerOrderDataHandler $customerOrderDataHandler
    ) {
    }

    public function getCustomerOrderById(int $id): ?CustomerOrder
    {
        return $this->customerOrderDataHandler->getCustomerOrderById($id);
    }

    /**
     * Get all Customer Orders for Ajax-Request.
     */
    public function getAllCustomerOrders(): JsonResponse
    {
        return $this->customerOrderDataHandler->getAllCustomerOrders();
    }

    /**
     * Get all Customer Order Pos for Ajax-Request.
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

    public function addCustomerOrder(CustomerOrder $customerOrder): void
    {
        $this->customerOrderDataHandler->addCustomerOrder($customerOrder);
    }

    public function updateCustomerOrder(CustomerOrder $customerOrder): void
    {
        $this->customerOrderDataHandler->updateCustomerOrder($customerOrder);
    }

    public function deleteCustomerOrder(CustomerOrder $customerOrder): void
    {
        $this->customerOrderDataHandler->deleteCustomerOrder($customerOrder);
    }
}
