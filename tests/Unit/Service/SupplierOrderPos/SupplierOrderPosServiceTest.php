<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\SupplierOrderPos;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Service\DataHandlers\SupplierOrderPos\SupplierOrderPosDataHandler;
use WebWMS\Service\SupplierOrderPos\SupplierOrderPosService;

/**
 * @package:    WebWMS\Tests\Unit\Service\SupplierOrderPos
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosServiceTest
 *
 * @covers \WebWMS\Service\SupplierOrderPos\SupplierOrderPosService
 */
final class SupplierOrderPosServiceTest extends TestCase
{
    private SupplierOrderPosService $supplierOrderPosService;

    /**
     * @var (SupplierOrderPosDataHandler&MockObject)|MockObject
     */
    private MockObject|SupplierOrderPosDataHandler $supplierOrderPosDataHandler;

    protected function setUp(): void
    {
        $this->supplierOrderPosDataHandler = $this->createMock(SupplierOrderPosDataHandler::class);
        $this->supplierOrderPosService = new SupplierOrderPosService($this->supplierOrderPosDataHandler);
    }

    public function testGetSupplierOrderPosById(): void
    {
        $supplierOrderPosId = 1;
        $supplierOrderPos = new SupplierOrderPos();

        $this->supplierOrderPosDataHandler
            ->expects(self::once())
            ->method('getSupplierOrderPosById')
            ->with($supplierOrderPosId)
            ->willReturn($supplierOrderPos);

        $result = $this->supplierOrderPosService->getSupplierOrderPosById($supplierOrderPosId);

        self::assertSame($supplierOrderPos, $result);
    }

    public function testGetSupplierOrderPosBySupplierOrderId(): void
    {
        $supplierOrderId = 1;
        $supplierOrderPos = new SupplierOrderPos();

        $this->supplierOrderPosDataHandler
            ->expects(self::once())
            ->method('getSupplierOrderPosBySupplierOrderId')
            ->with($supplierOrderId)
            ->willReturn($supplierOrderPos);

        $result = $this->supplierOrderPosService->getSupplierOrderPosBySupplierOrderId($supplierOrderId);

        self::assertSame($supplierOrderPos, $result);
    }

    public function testGetAllSupplierOrderPos(): void
    {
        $supplierOrderPosList = ['pos1', 'pos2'];

        $this->supplierOrderPosDataHandler
            ->expects(self::once())
            ->method('getAllSupplierOrderPos')
            ->willReturn(new JsonResponse($supplierOrderPosList));

        $result = $this->supplierOrderPosService->getAllSupplierOrderPos();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddSupplierOrderPos(): void
    {
        $supplierOrderPos = new SupplierOrderPos();
        $this->supplierOrderPosDataHandler
            ->expects(self::once())
            ->method('addSupplierOrderPos')
            ->with($supplierOrderPos);

        $this->supplierOrderPosService->addSupplierOrderPos($supplierOrderPos);
    }

    public function testUpdateSupplierOrderPos(): void
    {
        $supplierOrderPos = new SupplierOrderPos();
        $this->supplierOrderPosDataHandler
            ->expects(self::once())
            ->method('updateSupplierOrderPos')
            ->with($supplierOrderPos);

        $this->supplierOrderPosService->updateSupplierOrderPos($supplierOrderPos);
    }

    public function testDeleteSupplierOrderPos(): void
    {
        $supplierOrderPos = new SupplierOrderPos();
        $this->supplierOrderPosDataHandler
            ->expects(self::once())
            ->method('deleteSupplierOrderPos')
            ->with($supplierOrderPos);

        $this->supplierOrderPosService->deleteSupplierOrderPos($supplierOrderPos);
    }
}
