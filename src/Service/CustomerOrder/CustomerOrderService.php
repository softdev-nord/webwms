<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\CustomerOrder as CustomerOrders;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

/**
 * @package:    WebWMS\Service\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerOrderService
{
    public function __construct(
        private CustomerOrderDataHandler $customerOrderDataHandler
    ) {
    }

    public function getCustomerOrderById(int $id): ?CustomerOrders
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
        return $this->customerOrderDataHandler->getCustomerOrderPosByOrderId($id);
    }

    /**
     * Get last customer order id.
     */
    public function getLastCustomerOrderId(): array
    {
        return $this->customerOrderDataHandler->getLastCustomerOrderId();
    }

    public function addNewCustomerOrderAndRelatedPositions(Request $request): void
    {
        $this->customerOrderDataHandler->addNewCustomerOrderAndRelatedPositions($request);
    }
}
