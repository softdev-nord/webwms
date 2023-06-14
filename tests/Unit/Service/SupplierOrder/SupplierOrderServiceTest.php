<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\SupplierOrder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Service\DataHandlers\SupplierOrder\SupplierOrderDataHandler;
use WebWMS\Service\SupplierOrder\SupplierOrderService;

/**
 * @package:    WebWMS\Tests\Unit\Service\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderServiceTest
 *
 * @covers \WebWMS\Service\SupplierOrder\SupplierOrderService
 */
final class SupplierOrderServiceTest extends TestCase
{
    private SupplierOrderService $supplierOrderService;

    private MockObject $supplierOrderDataHandler;

    protected function setUp(): void
    {
        $this->supplierOrderDataHandler = $this->createMock(SupplierOrderDataHandler::class);
        $this->supplierOrderService = new SupplierOrderService($this->supplierOrderDataHandler);
    }

    public function testGetSupplierOrderById(): void
    {
        $orderId = 100001;
        $supplierOrder = new SupplierOrder();

        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('getSupplierOrderById')
            ->with($orderId)
            ->willReturn($supplierOrder);

        $result = $this->supplierOrderService->getSupplierOrderById($orderId);

        self::assertSame($supplierOrder, $result);
    }

    public function testGetSupplierOrderByNr(): void
    {
        $orderNr = '100001';
        $supplierOrder = new SupplierOrder();

        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('getSupplierOrderByNr')
            ->with($orderNr)
            ->willReturn($supplierOrder);

        $result = $this->supplierOrderService->getSupplierOrderByNr($orderNr);

        self::assertSame($supplierOrder, $result);
    }

    public function testGetAllSupplierOrders(): void
    {
        $supplierOrders = [
            new SupplierOrder(),
            new SupplierOrder(),
        ];

        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('getAllSupplierOrder')
            ->willReturn(new JsonResponse($supplierOrders));

        $result = $this->supplierOrderService->getAllSupplierOrder();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetLastSupplierOrderId(): void
    {
        $lastSupplierOrderId = [1, 2, 3];

        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('getLastSupplierOrderId')
            ->willReturn($lastSupplierOrderId);

        $result = $this->supplierOrderService->getLastSupplierOrderId();

        self::assertSame($lastSupplierOrderId, $result);
    }

    public function testAddSupplierOrder(): void
    {
        $supplierOrder = new SupplierOrder();
        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('addSupplierOrder')
            ->with($supplierOrder);

        $this->supplierOrderService->addSupplierOrder($supplierOrder);
    }

    public function testUpdateSupplierOrder(): void
    {
        $supplierOrder = new SupplierOrder();
        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('updateSupplierOrder')
            ->with($supplierOrder);

        $this->supplierOrderService->updateSupplierOrder($supplierOrder);
    }

    public function testDeleteSupplierOrder(): void
    {
        $supplierOrder = new SupplierOrder();
        $this->supplierOrderDataHandler
            ->expects(self::once())
            ->method('deleteSupplierOrder')
            ->with($supplierOrder);

        $this->supplierOrderService->deleteSupplierOrder($supplierOrder);
    }
}
