<?php

declare(strict_types=1);

namespace WebWMS\Service\CustomerOrder;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\CustomerOrder as CustomerOrders;
use WebWMS\Repository\CustomerOrderRepository;
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
        private CustomerOrderRepository $customerOrderRepository,
        private CustomerOrderDataHandler $customerOrderDataHandler
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getCustomerOrderApi(int $customerOrderId): ?CustomerOrders
    {
        $customerOrder = $this->customerOrderDataHandler->getCustomerOrderById($customerOrderId);

        if (!$customerOrder) {
            throw new EntityNotFoundException('Customer order with id '.$customerOrderId.' does not exist!');
        }

        return $customerOrder;
    }

    public function getCustomerOrderById(int $id): ?CustomerOrders
    {
        return $this->customerOrderDataHandler->getCustomerOrderById($id);
    }

    /**
     * @throws \Exception
     */
    public function getAllCustomerOrdersApi(): array
    {
        return $this->customerOrderRepository->findBy([], ['customerOrderId' => 'ASC']);
    }

    public function addCustomerOrderApi(
        int $customerOrderId,
        int $usrId,
        int $customerId,
        string $customerOrderNr,
        string $customerOrderReference,
               $customerOrderDate,
               $customerOrderOrderDate
    ): CustomerOrders {
        $createdAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $customerOrder = new CustomerOrders();
        $customerOrder->setCustomerOrderId($customerOrderId);
        $customerOrder->setUsrId($usrId);
        $customerOrder->setCustomerId($customerId);
        $customerOrder->setCustomerOrderNr($customerOrderNr);
        $customerOrder->setCustomerOrderReference($customerOrderReference);
        $customerOrder->setCustomerOrderDate($customerOrderDate);
        $customerOrder->setCustomerOrderCreationDate($customerOrderOrderDate);
        $customerOrder->setCreatedAt($createdAt);
        $this->customerOrderDataHandler->save($customerOrder);

        return $customerOrder;
    }

    public function updateCustomerOrderApi(
        int $customerOrderMainId,
        int $customerOrderId,
        int $usrId,
        int $customerId,
        string $customerOrderNr,
        string $customerOrderReference,
               $customerOrderDate,
               $customerOrderCreationDate
    ): ?CustomerOrders {
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $customerOrder = $this->customerOrderDataHandler->getCustomerOrderById($customerOrderMainId);

        $customerOrder->setCustomerOrderId($customerOrderId);
        $customerOrder->setUsrId($usrId);
        $customerOrder->setCustomerId($customerId);
        $customerOrder->setCustomerOrderNr($customerOrderNr);
        $customerOrder->setCustomerOrderReference($customerOrderReference);
        $customerOrder->setCustomerOrderDate($customerOrderDate);
        $customerOrder->setCustomerOrderCreationDate($customerOrderCreationDate);
        $customerOrder->setUpdatedAt($updatedAt);
        $this->customerOrderDataHandler->save($customerOrder);

        return $customerOrder;
    }

    /**
     * @throws EntityNotFoundException
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function deleteCustomerOrderApi(int $customerOrderId): void
    {
        $customerOrder = $this->customerOrderDataHandler->getCustomerOrderById($customerOrderId);

        if (!$customerOrder) {
            throw new EntityNotFoundException('Supplier with id '.$customerOrderId.' does not exist!');
        } else {
            $this->customerOrderDataHandler->delete($customerOrder);
        }
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
