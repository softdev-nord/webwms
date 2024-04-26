<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Service\DataHandlers\SupplierOrder\SupplierOrderDataHandler;
use WebWMS\Service\SupplierOrder\SupplierOrderService;

/**
 * @package:    WebWMS\Tests\Unit\Service\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        SupplierOrderServiceTest
 */
#[CoversClass(SupplierOrderService::class)]
final class SupplierOrderServiceTest extends TestCase
{
    private SupplierOrderService $supplierOrderService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(SupplierOrderDataHandler::class);
        $this->supplierOrderService = new SupplierOrderService($this->mockObject);
    }

    public function testGetSupplierOrderById(): void
    {
        $orderId = 100001;
        $supplierOrderEntity = new SupplierOrderEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getSupplierOrderById')
            ->with($orderId)
            ->willReturn($supplierOrderEntity);

        $result = $this->supplierOrderService->getSupplierOrderById($orderId);

        self::assertSame($supplierOrderEntity, $result);
    }

    public function testGetSupplierOrderByNr(): void
    {
        $orderNr = '100001';
        $supplierOrderEntity = new SupplierOrderEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getSupplierOrderByNr')
            ->with($orderNr)
            ->willReturn($supplierOrderEntity);

        $result = $this->supplierOrderService->getSupplierOrderByNr($orderNr);

        self::assertSame($supplierOrderEntity, $result);
    }

    public function testGetAllSupplierOrders(): void
    {
        $supplierOrders = [
            new SupplierOrderEntity(),
            new SupplierOrderEntity(),
        ];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllSupplierOrder')
            ->willReturn(new JsonResponse($supplierOrders));

        $result = $this->supplierOrderService->getAllSupplierOrder();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetLastSupplierOrderId(): void
    {
        $lastSupplierOrderId = [1, 2, 3];

        $this->mockObject
            ->expects(self::once())
            ->method('getLastSupplierOrderId')
            ->willReturn($lastSupplierOrderId);

        $result = $this->supplierOrderService->getLastSupplierOrderId();

        self::assertSame($lastSupplierOrderId, $result);
    }

    public function testAddSupplierOrder(): void
    {
        $supplierOrderEntity = new SupplierOrderEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addSupplierOrder')
            ->with($supplierOrderEntity);

        $this->supplierOrderService->addSupplierOrder($supplierOrderEntity);
    }

    public function testUpdateSupplierOrder(): void
    {
        $supplierOrderEntity = new SupplierOrderEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateSupplierOrder')
            ->with($supplierOrderEntity);

        $this->supplierOrderService->updateSupplierOrder($supplierOrderEntity);
    }

    public function testDeleteSupplierOrder(): void
    {
        $supplierOrderEntity = new SupplierOrderEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteSupplierOrder')
            ->with($supplierOrderEntity);

        $this->supplierOrderService->deleteSupplierOrder($supplierOrderEntity);
    }
}
