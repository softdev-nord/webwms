<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\CustomerOrderPos;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\CustomerOrderPos\CustomerOrderPosService;
use WebWMS\Service\DataHandlers\CustomerOrderPos\CustomerOrderPosDataHandler;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\CustomerOrderPos',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'CustomerOrderPosServiceTest'
)]
#[CoversClass(CustomerOrderPosService::class)]
final class CustomerOrderPosServiceTest extends TestCase
{
    private CustomerOrderPosService $customerOrderPosService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(CustomerOrderPosDataHandler::class);
        $this->customerOrderPosService = new CustomerOrderPosService($this->mockObject);
    }

    public function testGetCustomerOrderPosById(): void
    {
        $customerOrderPosId = 1;
        $customerOrderPos = new CustomerOrderPos();

        $this->mockObject
            ->expects($this->once())
            ->method('getCustomerOrderPosById')
            ->with($customerOrderPosId)
            ->willReturn($customerOrderPos);

        $result = $this->customerOrderPosService->getCustomerOrderPosById($customerOrderPosId);

        self::assertSame($customerOrderPos, $result);
    }

    public function testGetCustomerOrderPosByCustomerOrderId(): void
    {
        $customerOrderId = 1;
        $customerOrderPos = new CustomerOrderPos();

        $this->mockObject
            ->expects($this->once())
            ->method('getCustomerOrderPosByCustomerOrderId')
            ->with($customerOrderId)
            ->willReturn($customerOrderPos);

        $result = $this->customerOrderPosService->getCustomerOrderPosByCustomerOrderId($customerOrderId);

        self::assertSame($customerOrderPos, $result);
    }

    public function testGetAllCustomerOrderPos(): void
    {
        $customerOrderPosList = ['pos1', 'pos2'];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllCustomerOrderPos')
            ->willReturn(new JsonResponse($customerOrderPosList));

        $jsonResponse = $this->customerOrderPosService->getAllCustomerOrderPos();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testAddCustomerOrderPos(): void
    {
        $customerOrderPos = new CustomerOrderPos();
        $this->mockObject
            ->expects($this->once())
            ->method('addCustomerOrderPos')
            ->with($customerOrderPos);

        $this->customerOrderPosService->addCustomerOrderPos($customerOrderPos);
    }

    public function testUpdateCustomerOrderPos(): void
    {
        $customerOrderPos = new CustomerOrderPos();
        $this->mockObject
            ->expects($this->once())
            ->method('updateCustomerOrderPos')
            ->with($customerOrderPos);

        $this->customerOrderPosService->updateCustomerOrderPos($customerOrderPos);
    }

    public function testDeleteCustomerOrderPos(): void
    {
        $customerOrderPos = new CustomerOrderPos();
        $this->mockObject
            ->expects($this->once())
            ->method('deleteCustomerOrderPos')
            ->with($customerOrderPos);

        $this->customerOrderPosService->deleteCustomerOrderPos($customerOrderPos);
    }
}
