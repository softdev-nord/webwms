<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\CustomerOrder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Service\CustomerOrder\CustomerOrderService;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderServiceTest
 *
 * @covers \WebWMS\Service\CustomerOrder\CustomerOrderService
 */
final class CustomerOrderServiceTest extends TestCase
{
    private CustomerOrderService $customerOrderService;

    private MockObject $customerOrderDataHandler;

    protected function setUp(): void
    {
        $this->customerOrderDataHandler = $this->createMock(CustomerOrderDataHandler::class);
        $this->customerOrderService = new CustomerOrderService($this->customerOrderDataHandler);
    }

    public function testGetCustomerOrderById(): void
    {
        $orderId = 1;
        $customerOrder = new CustomerOrder();

        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('getCustomerOrderById')
            ->with($orderId)
            ->willReturn($customerOrder);

        $result = $this->customerOrderService->getCustomerOrderById($orderId);

        self::assertSame($customerOrder, $result);
    }

    public function testGetAllCustomerOrders(): void
    {
        $customerOrders = [
            new CustomerOrder(),
            new CustomerOrder(),
        ];

        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('getAllCustomerOrders')
            ->willReturn(new JsonResponse($customerOrders));

        $result = $this->customerOrderService->getAllCustomerOrders();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetAllCustomerOrderPos(): void
    {
        $customerOrderPos = [
            new CustomerOrderPos(),
            new CustomerOrderPos(),
        ];

        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('getAllCustomerOrderPos')
            ->willReturn(new JsonResponse($customerOrderPos));

        $result = $this->customerOrderService->getAllCustomerOrderPos();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetCustomerOrderPosByOrderId(): void
    {
        $orderId = 1;
        $customerOrderPos = ['pos1', 'pos2'];

        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('getCustomerOrderPosByCustomerOrderId')
            ->with($orderId)
            ->willReturn(new JsonResponse($customerOrderPos));

        $result = $this->customerOrderService->getCustomerOrderPosByOrderId($orderId);

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetLastCustomerOrderId(): void
    {
        $lastCustomerOrderId = [1, 2, 3];

        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('getLastCustomerOrderId')
            ->willReturn($lastCustomerOrderId);

        $result = $this->customerOrderService->getLastCustomerOrderId();

        self::assertSame($lastCustomerOrderId, $result);
    }

    public function testAddCustomerOrder(): void
    {
        $customerOrder = new CustomerOrder();
        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('addCustomerOrder')
            ->with($customerOrder);

        $this->customerOrderService->addCustomerOrder($customerOrder);
    }

    public function testUpdateCustomerOrder(): void
    {
        $customerOrder = new CustomerOrder();
        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('updateCustomerOrder')
            ->with($customerOrder);

        $this->customerOrderService->updateCustomerOrder($customerOrder);
    }

    public function testDeleteCustomerOrder(): void
    {
        $customerOrder = new CustomerOrder();
        $this->customerOrderDataHandler
            ->expects(self::once())
            ->method('deleteCustomerOrder')
            ->with($customerOrder);

        $this->customerOrderService->deleteCustomerOrder($customerOrder);
    }
}
