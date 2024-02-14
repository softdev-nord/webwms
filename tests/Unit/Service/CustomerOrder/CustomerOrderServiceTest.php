<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\CustomerOrder;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Service\CustomerOrder\CustomerOrderService;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderServiceTest
 */
#[CoversClass(CustomerOrderService::class)]
final class CustomerOrderServiceTest extends TestCase
{
    private CustomerOrderService $customerOrderService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(CustomerOrderDataHandler::class);
        $this->customerOrderService = new CustomerOrderService($this->mockObject);
    }

    public function testGetCustomerOrderById(): void
    {
        $orderId = 1;
        $customerOrderEntity = new CustomerOrderEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getCustomerOrderById')
            ->with($orderId)
            ->willReturn($customerOrderEntity);

        $result = $this->customerOrderService->getCustomerOrderById($orderId);

        self::assertSame($customerOrderEntity, $result);
    }

    public function testGetAllCustomerOrders(): void
    {
        $customerOrders = [
            new CustomerOrderEntity(),
            new CustomerOrderEntity(),
        ];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllCustomerOrders')
            ->willReturn(new JsonResponse($customerOrders));

        $result = $this->customerOrderService->getAllCustomerOrders();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetAllCustomerOrderPos(): void
    {
        $customerOrderPos = [
            new CustomerOrderPosEntity(),
            new CustomerOrderPosEntity(),
        ];

        $this->mockObject
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

        $this->mockObject
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

        $this->mockObject
            ->expects(self::once())
            ->method('getLastCustomerOrderId')
            ->willReturn($lastCustomerOrderId);

        $result = $this->customerOrderService->getLastCustomerOrderId();

        self::assertSame($lastCustomerOrderId, $result);
    }

    public function testAddCustomerOrder(): void
    {
        $customerOrderEntity = new CustomerOrderEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addCustomerOrder')
            ->with($customerOrderEntity);

        $this->customerOrderService->addCustomerOrder($customerOrderEntity);
    }

    public function testUpdateCustomerOrder(): void
    {
        $customerOrderEntity = new CustomerOrderEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateCustomerOrder')
            ->with($customerOrderEntity);

        $this->customerOrderService->updateCustomerOrder($customerOrderEntity);
    }

    public function testDeleteCustomerOrder(): void
    {
        $customerOrderEntity = new CustomerOrderEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteCustomerOrder')
            ->with($customerOrderEntity);

        $this->customerOrderService->deleteCustomerOrder($customerOrderEntity);
    }
}
