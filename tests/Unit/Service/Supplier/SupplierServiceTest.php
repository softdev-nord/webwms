<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Supplier;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;
use WebWMS\Service\Supplier\SupplierService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Supplier',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierServiceTest'
)]
#[CoversClass(SupplierService::class)]
final class SupplierServiceTest extends TestCase
{
    private SupplierService $supplierService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(SupplierDataHandler::class);
        $this->supplierService = new SupplierService($this->mockObject);
    }

    public function testGetSupplierById(): void
    {
        $supplierId = 1;
        $supplier = new Supplier();

        $this->mockObject->expects($this->once())
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

        $this->mockObject->expects($this->once())
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

        $this->mockObject
            ->expects($this->once())
            ->method('getAllSuppliers')
            ->willReturn($suppliers);

        $jsonResponse = $this->supplierService->getAllSuppliers();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetAllSuppliersAjax(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);
        $supplierNrInput = '123';

        $this->mockObject->expects($this->once())
            ->method('getSuppliers')
            ->willReturn($jsonResponse);

        $result = $this->supplierService->getAllSuppliersAjax($supplierNrInput);

        self::assertSame($jsonResponse, $result);
    }

    public function testAddSupplier(): void
    {
        $supplier = new Supplier();
        $this->mockObject
            ->expects($this->once())
            ->method('addSupplier')
            ->with($supplier);

        $this->supplierService->addSupplier($supplier);
    }

    public function testUpdateSupplier(): void
    {
        $supplier = new Supplier();
        $this->mockObject
            ->expects($this->once())
            ->method('updateSupplier')
            ->with($supplier);

        $this->supplierService->updateSupplier($supplier);
    }

    public function testDeleteSupplier(): void
    {
        $supplier = new Supplier();
        $this->mockObject
            ->expects($this->once())
            ->method('deleteSupplier')
            ->with($supplier);

        $this->supplierService->deleteSupplier($supplier);
    }

    public function testGetLastSupplier(): void
    {
        $lastSupplierId = 10;

        $this->mockObject->expects($this->once())
            ->method('getLastSupplier')
            ->willReturn($lastSupplierId);

        $result = $this->supplierService->getLastSupplier();

        self::assertSame($lastSupplierId, $result);
    }
}
