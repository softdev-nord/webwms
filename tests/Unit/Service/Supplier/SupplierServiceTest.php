<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Supplier;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;
use WebWMS\Service\Supplier\SupplierService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierServiceTest
 *
 * @covers \WebWMS\Service\Supplier\SupplierService
 */
final class SupplierServiceTest extends TestCase
{
    private SupplierService $supplierService;

    private MockObject $supplierDataHandler;

    protected function setUp(): void
    {
        $this->supplierDataHandler = $this->createMock(SupplierDataHandler::class);
        $this->supplierService = new SupplierService($this->supplierDataHandler);
    }

    public function testGetSupplierById(): void
    {
        $supplierId = 1;
        $supplier = new Supplier();

        $this->supplierDataHandler->expects(self::once())
            ->method('getSupplierById')
            ->with($supplierId)
            ->willReturn($supplier);

        $result = $this->supplierService->getSupplierById($supplierId);

        self::assertSame($supplier, $result);
    }

    public function testGetSupplierByNr(): void
    {
        $supplierNr = 123;
        $supplier = new Supplier();

        $this->supplierDataHandler->expects(self::once())
            ->method('getSupplierByNr')
            ->with($supplierNr)
            ->willReturn($supplier);

        $result = $this->supplierService->getSupplierByNr($supplierNr);

        self::assertSame($supplier, $result);
    }

    public function testGetAllSuppliers(): void
    {
        $suppliers = [
            new Supplier(),
            new Supplier(),
        ];

        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getAllSuppliers')
            ->willReturn($suppliers);

        $result = $this->supplierService->getAllSuppliers();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetAllSuppliersAjax(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);

        $this->supplierDataHandler->expects(self::once())
            ->method('getSuppliers')
            ->willReturn($jsonResponse);

        $result = $this->supplierService->getAllSuppliersAjax();

        self::assertSame($jsonResponse, $result);
    }

    public function testAddSupplierCallsDataHandlerMethod(): void
    {
        $article = new Supplier();
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('addSupplier')
            ->with($article);

        $this->supplierService->addSupplier($article);
    }

    public function testUpdateSupplierCallsDataHandlerMethod(): void
    {
        $supplier = new Supplier();
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('updateSupplier')
            ->with($supplier);

        $this->supplierService->updateSupplier($supplier);
    }

    public function testDeleteSupplierCallsDataHandlerMethod(): void
    {
        $supplier = new Supplier();
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('deleteSupplier')
            ->with($supplier);

        $this->supplierService->deleteSupplier($supplier);
    }

    public function testGetLastSupplier(): void
    {
        $lastSupplierId = 10;

        $this->supplierDataHandler->expects(self::once())
            ->method('getLastSupplier')
            ->willReturn($lastSupplierId);

        $result = $this->supplierService->getLastSupplier();

        self::assertSame($lastSupplierId, $result);
    }
}
