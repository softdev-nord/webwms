<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\CustomerOrderPos;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Service\CustomerOrderPos\CustomerOrderPosService;
use WebWMS\Service\DataHandlers\CustomerOrderPos\CustomerOrderPosDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\CustomerOrderPosEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosServiceTest
 */
#[CoversClass(CustomerOrderPosService::class)]
final class CustomerOrderPosServiceTest extends TestCase
{
    private CustomerOrderPosService $customerOrderPosService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(CustomerOrderPosDataHandler::class);
        $this->customerOrderPosService = new CustomerOrderPosService($this->mockObject);
    }

    public function testGetCustomerOrderPosById(): void
    {
        $customerOrderPosId = 1;
        $customerOrderPosEntity = new CustomerOrderPosEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getCustomerOrderPosById')
            ->with($customerOrderPosId)
            ->willReturn($customerOrderPosEntity);

        $result = $this->customerOrderPosService->getCustomerOrderPosById($customerOrderPosId);

        self::assertSame($customerOrderPosEntity, $result);
    }

    public function testGetCustomerOrderPosByCustomerOrderId(): void
    {
        $customerOrderId = 1;
        $customerOrderPosEntity = new CustomerOrderPosEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getCustomerOrderPosByCustomerOrderId')
            ->with($customerOrderId)
            ->willReturn($customerOrderPosEntity);

        $result = $this->customerOrderPosService->getCustomerOrderPosByCustomerOrderId($customerOrderId);

        self::assertSame($customerOrderPosEntity, $result);
    }

    public function testGetAllCustomerOrderPos(): void
    {
        $customerOrderPosList = ['pos1', 'pos2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllCustomerOrderPos')
            ->willReturn(new JsonResponse($customerOrderPosList));

        $result = $this->customerOrderPosService->getAllCustomerOrderPos();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddCustomerOrderPos(): void
    {
        $customerOrderPosEntity = new CustomerOrderPosEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addCustomerOrderPos')
            ->with($customerOrderPosEntity);

        $this->customerOrderPosService->addCustomerOrderPos($customerOrderPosEntity);
    }

    public function testUpdateCustomerOrderPos(): void
    {
        $customerOrderPosEntity = new CustomerOrderPosEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateCustomerOrderPos')
            ->with($customerOrderPosEntity);

        $this->customerOrderPosService->updateCustomerOrderPos($customerOrderPosEntity);
    }

    public function testDeleteCustomerOrderPos(): void
    {
        $customerOrderPosEntity = new CustomerOrderPosEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteCustomerOrderPos')
            ->with($customerOrderPosEntity);

        $this->customerOrderPosService->deleteCustomerOrderPos($customerOrderPosEntity);
    }
}
