<?php

declare(strict_types=1);

namespace WebWMS\Tests\Functional\Service\Supplier;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;
use WebWMS\Service\Supplier\SupplierService;

/**
 * @package:    WebWMS\Tests\Functional\Service\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierServiceTest
 *
 * @covers \WebWMS\Service\Supplier\SupplierService
 */
final class SupplierServiceTest extends TestCase
{
    /**
     * @var (SupplierDataHandler&MockObject)|MockObject
     */
    private MockObject|SupplierDataHandler $supplierDataHandler;
    private SupplierService $supplierService;

    protected function setUp(): void
    {
        $this->supplierDataHandler = $this->createMock(SupplierDataHandler::class);
        $this->supplierService = new SupplierService($this->supplierDataHandler);
    }

    public function testGetSupplierByIdReturnsNullWhenSupplierDoesNotExist(): void
    {
        $supplierId = 1;
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getSupplierById')
            ->with($supplierId)
            ->willReturn(null);

        $result = $this->supplierService->getSupplierById($supplierId);

        self::assertNull($result);
    }

    public function testGetSupplierByIdReturnsSupplierWhenSupplierExists(): void
    {
        $supplierId = 1;
        $supplier = new Supplier();
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getSupplierById')
            ->with($supplierId)
            ->willReturn($supplier);

        $result = $this->supplierService->getSupplierById($supplierId);

        self::assertSame($supplier, $result);
    }

    public function testGetSupplierByNrReturnsNullWhenSupplierDoesNotExist(): void
    {
        $supplierNr = 60000;
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getSupplierByNr')
            ->with($supplierNr)
            ->willReturn(null);

        $result = $this->supplierService->getSupplierByNr($supplierNr);

        self::assertNull($result);
    }

    public function testGetSupplierByNrReturnsSupplierWhenSupplierExists(): void
    {
        $supplierNr = 60000;
        $supplier = new Supplier();
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getSupplierByNr')
            ->with($supplierNr)
            ->willReturn($supplier);

        $result = $this->supplierService->getSupplierByNr($supplierNr);

        self::assertSame($supplier, $result);
    }

    public function testGetAllSuppliersReturnsJsonResponse(): void
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

    public function testGetAllSuppliersAjaxReturnsJsonResponse(): void
    {
        $suppliers = [
            new Supplier(),
            new Supplier(),
        ];
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getSuppliers')
            ->willReturn(new JsonResponse($suppliers));

        $result = $this->supplierService->getAllSuppliersAjax();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddSupplierCallsDataHandlerMethod(): void
    {
        $supplier = new Supplier();
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('addSupplier')
            ->with($supplier);

        $this->supplierService->addSupplier($supplier);
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

    public function testGetLastSupplierReturnsInt(): void
    {
        $lastSupplierId = 5;
        $this->supplierDataHandler
            ->expects(self::once())
            ->method('getLastSupplier')
            ->willReturn($lastSupplierId);

        $result = $this->supplierService->getLastSupplier();

        self::assertIsInt($result);
    }
}
