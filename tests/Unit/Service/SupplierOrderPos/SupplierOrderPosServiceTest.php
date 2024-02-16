<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\SupplierOrderPos;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderPosEntity;
use WebWMS\Service\DataHandlers\SupplierOrderPos\SupplierOrderPosDataHandler;
use WebWMS\Service\SupplierOrderPos\SupplierOrderPosService;

/**
 * @package:    WebWMS\Tests\Unit\Service\SupplierOrderPosEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosServiceTest
 */
#[CoversClass(SupplierOrderPosService::class)]
final class SupplierOrderPosServiceTest extends TestCase
{
    private SupplierOrderPosService $supplierOrderPosService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(SupplierOrderPosDataHandler::class);
        $this->supplierOrderPosService = new SupplierOrderPosService($this->mockObject);
    }

    public function testGetSupplierOrderPosById(): void
    {
        $supplierOrderPosId = 1;
        $supplierOrderPosEntity = new SupplierOrderPosEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getSupplierOrderPosById')
            ->with($supplierOrderPosId)
            ->willReturn($supplierOrderPosEntity);

        $result = $this->supplierOrderPosService->getSupplierOrderPosById($supplierOrderPosId);

        self::assertSame($supplierOrderPosEntity, $result);
    }

    public function testGetSupplierOrderPosBySupplierOrderId(): void
    {
        $supplierOrderId = 1;
        $supplierOrderPosEntity = new SupplierOrderPosEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getSupplierOrderPosBySupplierOrderId')
            ->with($supplierOrderId)
            ->willReturn($supplierOrderPosEntity);

        $result = $this->supplierOrderPosService->getSupplierOrderPosBySupplierOrderId($supplierOrderId);

        self::assertSame($supplierOrderPosEntity, $result);
    }

    public function testGetAllSupplierOrderPos(): void
    {
        $supplierOrderPosList = ['pos1', 'pos2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllSupplierOrderPos')
            ->willReturn(new JsonResponse($supplierOrderPosList));

        $result = $this->supplierOrderPosService->getAllSupplierOrderPos();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddSupplierOrderPos(): void
    {
        $supplierOrderPosEntity = new SupplierOrderPosEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addSupplierOrderPos')
            ->with($supplierOrderPosEntity);

        $this->supplierOrderPosService->addSupplierOrderPos($supplierOrderPosEntity);
    }

    public function testUpdateSupplierOrderPos(): void
    {
        $supplierOrderPosEntity = new SupplierOrderPosEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateSupplierOrderPos')
            ->with($supplierOrderPosEntity);

        $this->supplierOrderPosService->updateSupplierOrderPos($supplierOrderPosEntity);
    }

    public function testDeleteSupplierOrderPos(): void
    {
        $supplierOrderPosEntity = new SupplierOrderPosEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteSupplierOrderPos')
            ->with($supplierOrderPosEntity);

        $this->supplierOrderPosService->deleteSupplierOrderPos($supplierOrderPosEntity);
    }
}
