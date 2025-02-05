<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\CustomerOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\CustomerOrder\CustomerOrderService;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\CustomerOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'CustomerOrderServiceTest'
)]
#[CoversClass(CustomerOrderService::class)]
final class CustomerOrderServiceTest extends TestCase
{
    private CustomerOrderService $customerOrderService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(CustomerOrderDataHandler::class);
        $this->customerOrderService = new CustomerOrderService($this->mockObject);
    }

    public function testGetCustomerOrderById(): void
    {
        $orderId = 1;
        $customerOrder = new CustomerOrder();

        $this->mockObject
            ->expects($this->once())
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

        $this->mockObject
            ->expects($this->once())
            ->method('getAllCustomerOrders')
            ->willReturn(new JsonResponse($customerOrders));

        $jsonResponse = $this->customerOrderService->getAllCustomerOrders();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetAllCustomerOrderPos(): void
    {
        $customerOrderPos = [
            new CustomerOrderPos(),
            new CustomerOrderPos(),
        ];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllCustomerOrderPos')
            ->willReturn(new JsonResponse($customerOrderPos));

        $jsonResponse = $this->customerOrderService->getAllCustomerOrderPos();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetCustomerOrderPosByOrderId(): void
    {
        $orderId = 1;
        $customerOrderPos = ['pos1', 'pos2'];

        $this->mockObject
            ->expects($this->once())
            ->method('getCustomerOrderPosByCustomerOrderId')
            ->with($orderId)
            ->willReturn(new JsonResponse($customerOrderPos));

        $jsonResponse = $this->customerOrderService->getCustomerOrderPosByOrderId($orderId);

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetLastCustomerOrderId(): void
    {
        $lastCustomerOrderId = [1, 2, 3];

        $this->mockObject
            ->expects($this->once())
            ->method('getLastCustomerOrderId')
            ->willReturn($lastCustomerOrderId);

        $result = $this->customerOrderService->getLastCustomerOrderId();

        self::assertSame($lastCustomerOrderId, $result);
    }

    public function testAddCustomerOrder(): void
    {
        $customerOrder = new CustomerOrder();
        $this->mockObject
            ->expects($this->once())
            ->method('addCustomerOrder')
            ->with($customerOrder);

        $this->customerOrderService->addCustomerOrder($customerOrder);
    }

    public function testUpdateCustomerOrder(): void
    {
        $customerOrder = new CustomerOrder();
        $this->mockObject
            ->expects($this->once())
            ->method('updateCustomerOrder')
            ->with($customerOrder);

        $this->customerOrderService->updateCustomerOrder($customerOrder);
    }

    public function testDeleteCustomerOrder(): void
    {
        $customerOrder = new CustomerOrder();
        $this->mockObject
            ->expects($this->once())
            ->method('deleteCustomerOrder')
            ->with($customerOrder);

        $this->customerOrderService->deleteCustomerOrder($customerOrder);
    }
}
