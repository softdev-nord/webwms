<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\CustomerOrderPos;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Service\CustomerOrderPos\CustomerOrderPosService;
use WebWMS\Service\DataHandlers\CustomerOrderPos\CustomerOrderPosDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\CustomerOrderPos
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosServiceTest
 *
 * @covers \WebWMS\Service\CustomerOrderPos\CustomerOrderPosService
 */
final class CustomerOrderPosServiceTest extends TestCase
{
    private CustomerOrderPosService $customerOrderPosService;

    /**
     * @var (CustomerOrderPosDataHandler&MockObject)|MockObject
     */
    private MockObject|CustomerOrderPosDataHandler $customerOrderPosDataHandler;

    protected function setUp(): void
    {
        $this->customerOrderPosDataHandler = $this->createMock(CustomerOrderPosDataHandler::class);
        $this->customerOrderPosService = new CustomerOrderPosService($this->customerOrderPosDataHandler);
    }

    public function testGetCustomerOrderPosById(): void
    {
        $customerOrderPosId = 1;
        $customerOrderPos = new CustomerOrderPos();

        $this->customerOrderPosDataHandler
            ->expects(self::once())
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

        $this->customerOrderPosDataHandler
            ->expects(self::once())
            ->method('getCustomerOrderPosByCustomerOrderId')
            ->with($customerOrderId)
            ->willReturn($customerOrderPos);

        $result = $this->customerOrderPosService->getCustomerOrderPosByCustomerOrderId($customerOrderId);

        self::assertSame($customerOrderPos, $result);
    }

    public function testGetAllCustomerOrderPos(): void
    {
        $customerOrderPosList = ['pos1', 'pos2'];

        $this->customerOrderPosDataHandler
            ->expects(self::once())
            ->method('getAllCustomerOrderPos')
            ->willReturn(new JsonResponse($customerOrderPosList));

        $result = $this->customerOrderPosService->getAllCustomerOrderPos();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddCustomerOrderPos(): void
    {
        $customerOrderPos = new CustomerOrderPos();
        $this->customerOrderPosDataHandler
            ->expects(self::once())
            ->method('addCustomerOrderPos')
            ->with($customerOrderPos);

        $this->customerOrderPosService->addCustomerOrderPos($customerOrderPos);
    }

    public function testUpdateCustomerOrderPos(): void
    {
        $customerOrderPos = new CustomerOrderPos();
        $this->customerOrderPosDataHandler
            ->expects(self::once())
            ->method('updateCustomerOrderPos')
            ->with($customerOrderPos);

        $this->customerOrderPosService->updateCustomerOrderPos($customerOrderPos);
    }

    public function testDeleteCustomerOrderPos(): void
    {
        $customerOrderPos = new CustomerOrderPos();
        $this->customerOrderPosDataHandler
            ->expects(self::once())
            ->method('deleteCustomerOrderPos')
            ->with($customerOrderPos);

        $this->customerOrderPosService->deleteCustomerOrderPos($customerOrderPos);
    }
}
