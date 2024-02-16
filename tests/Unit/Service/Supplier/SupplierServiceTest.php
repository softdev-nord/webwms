<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Supplier;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierEntity;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;
use WebWMS\Service\Supplier\SupplierService;

/**
 * @package:    WebWMS\Tests\Unit\Service\SupplierEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierServiceTest
 */
#[CoversClass(SupplierService::class)]
final class SupplierServiceTest extends TestCase
{
    private SupplierService $supplierService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(SupplierDataHandler::class);
        $this->supplierService = new SupplierService($this->mockObject);
    }

    public function testGetSupplierById(): void
    {
        $supplierId = 1;
        $supplierEntity = new SupplierEntity();

        $this->mockObject->expects(self::once())
            ->method('getSupplierById')
            ->with($supplierId)
            ->willReturn($supplierEntity);

        $result = $this->supplierService->getSupplierById($supplierId);

        self::assertSame($supplierEntity, $result);
    }

    public function testGetSupplierByNr(): void
    {
        $supplierNr = 123;
        $supplierEntity = new SupplierEntity();

        $this->mockObject->expects(self::once())
            ->method('getSupplierByNr')
            ->with($supplierNr)
            ->willReturn($supplierEntity);

        $result = $this->supplierService->getSupplierByNr($supplierNr);

        self::assertSame($supplierEntity, $result);
    }

    public function testGetAllSuppliers(): void
    {
        $suppliers = [
            new SupplierEntity(),
            new SupplierEntity(),
        ];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllSuppliers')
            ->willReturn($suppliers);

        $result = $this->supplierService->getAllSuppliers();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetAllSuppliersAjax(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);
        $supplierNrInput = '123';

        $this->mockObject->expects(self::once())
            ->method('getSuppliers')
            ->willReturn($jsonResponse);

        $result = $this->supplierService->getAllSuppliersAjax($supplierNrInput);

        self::assertSame($jsonResponse, $result);
    }

    public function testAddSupplier(): void
    {
        $supplierEntity = new SupplierEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addSupplier')
            ->with($supplierEntity);

        $this->supplierService->addSupplier($supplierEntity);
    }

    public function testUpdateSupplier(): void
    {
        $supplierEntity = new SupplierEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateSupplier')
            ->with($supplierEntity);

        $this->supplierService->updateSupplier($supplierEntity);
    }

    public function testDeleteSupplier(): void
    {
        $supplierEntity = new SupplierEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteSupplier')
            ->with($supplierEntity);

        $this->supplierService->deleteSupplier($supplierEntity);
    }

    public function testGetLastSupplier(): void
    {
        $lastSupplierId = 10;

        $this->mockObject->expects(self::once())
            ->method('getLastSupplier')
            ->willReturn($lastSupplierId);

        $result = $this->supplierService->getLastSupplier();

        self::assertSame($lastSupplierId, $result);
    }
}
