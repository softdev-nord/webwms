<?php

declare(strict_types=1);

namespace WebWMS\Tests\Functional\Service\DataHandlers\Supplier;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Repository\SupplierRepository;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Functional\Service\DataHandlers\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierDataHandlerTest.
 *
 * @covers \WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler
 */
final class SupplierDataHandlerTest extends KernelTestCase
{
    /**
     * @var (EntityManagerInterface&MockObject)|MockObject
     */
    private MockObject|EntityManagerInterface $entityManagerMock;

    /**
     * @var (MockObject&DateTimeService)|MockObject
     */
    private MockObject|DateTimeService $dateTimeServiceMock;

    private SupplierDataHandler $supplierDataHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dateTimeServiceMock = $this->getMockBuilder(DateTimeService::class)
            ->getMock();

        $this->supplierDataHandler = new SupplierDataHandler(
            $this->entityManagerMock,
            $this->dateTimeServiceMock,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->entityManagerMock);
        unset($this->dateTimeServiceMock);
        unset($this->supplierDataHandler);
    }

    public function testSave(): void
    {
        $supplier = new Supplier();

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('persist')
            ->with($supplier);
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $this->supplierDataHandler->save($supplier);
    }

    public function testDelete(): void
    {
        $supplier = new Supplier();

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('remove')
            ->with($supplier);
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $this->supplierDataHandler->delete($supplier);
    }

    public function testGetSupplierById(): void
    {
        $supplier = new Supplier();
        $supplierId = 1;
        $supplier->setSupplierId($supplierId);

        $repositoryMock = self::createMock(SupplierRepository::class);
        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['supplierId' => $supplierId])
            ->willReturn($supplier);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Supplier::class)
            ->willReturn($repositoryMock);

        $result = $this->supplierDataHandler->getSupplierById($supplierId);

        self::assertSame($supplier, $result);
    }

    public function testGetSupplierByNr(): void
    {
        $supplier = new Supplier();
        $supplierNr = 60000;
        $supplier->setSupplierNr($supplierNr);

        $repositoryMock = $this->createMock(SupplierRepository::class);
        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['supplierNr' => $supplierNr])
            ->willReturn($supplier);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Supplier::class)
            ->willReturn($repositoryMock);

        $result = $this->supplierDataHandler->getSupplierByNr($supplierNr);

        self::assertSame($supplier, $result);
    }

    public function testGetSuppliersReturnsJsonResponse(): void
    {
        $_GET['name_supplier'] = 'John';
        $_GET['numOfBoxSupplier'] = 'supplier_name';

        $stmt = $this->createMock(Result::class);
        $stmt->method('fetchAssociative')
            ->willReturn([
                'supplier_nr' => '1',
                'supplier_name' => 'John Doe',
                'supplier_address_addition' => '',
                'supplier_address_street' => '123 Main St',
                'supplier_address_street_nr' => '1A',
                'supplier_country_code' => 'US',
                'supplier_zip_code' => '12345',
                'supplier_city' => 'Anytown',
                'supplier_id' => 'abc123',
            ]);
        $connection = $this->createMock(Connection::class);
        $connection->method('executeQuery')
            ->with(self::stringContains('SELECT supplier_nr'))
            ->willReturn($stmt);
        $this->entityManagerMock->method('getConnection')
            ->willReturn($connection);

        $response = $this->supplierDataHandler->getSuppliers();
        self::assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode((string) $response->getContent(), true);
        self::assertIsArray($data);
    }

    public function testGetAllSuppliers(): void
    {
        $suppliers = [
            ['supplierId' => 1, 'name' => 'Supplier A'],
            ['supplierId' => 2, 'name' => 'Supplier B'],
        ];

        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock->method('getArrayResult')->willReturn($suppliers);
        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $queryBuilderMock->method('select')->willReturnSelf();
        $queryBuilderMock->method('from')->willReturnSelf();
        $queryBuilderMock->method('getQuery')->willReturn($queryMock);
        $this->entityManagerMock->method('createQueryBuilder')->willReturn($queryBuilderMock);

        $result = $this->supplierDataHandler->getAllSuppliers();

        self::assertIsArray($result);
        self::assertCount(2, $result);
        self::assertEquals('Supplier A', $result[0]['name']);
        self::assertEquals('Supplier B', $result[1]['name']);
    }

    public function testAddSupplier(): void
    {
        $createdAt = new \DateTime();
        $this->dateTimeServiceMock
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($createdAt);

        $supplierMock = $this->getMockBuilder(Supplier::class)
            ->getMock();
        $supplierMock
            ->expects(self::once())
            ->method('setCreatedAt')
            ->with($createdAt);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($supplierMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->supplierDataHandler->addSupplier($supplierMock);
    }

    public function testUpdateSupplier(): void
    {
        $updatedAt = new \DateTime();
        $this->dateTimeServiceMock
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($updatedAt);

        $supplierMock = $this->getMockBuilder(Supplier::class)
            ->getMock();
        $supplierMock
            ->expects(self::once())
            ->method('setUpdatedAt')
            ->with($updatedAt);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($supplierMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->supplierDataHandler->updateSupplier($supplierMock);
    }

    public function testDeleteSupplier(): void
    {
        $supplierMock = $this->getMockBuilder(Supplier::class)
            ->getMock();

        $this->entityManagerMock
            ->expects(self::once())
            ->method('remove')
            ->with($supplierMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->supplierDataHandler->deleteSupplier($supplierMock);
    }

//    public function testGetLastSupplierReturnsInt(): void
//    {
//        $result = [['supplierId' => '5']];
//        $queryMock = $this->createMock(AbstractQuery::class);
//        $queryMock->method('getArrayResult')->willReturn($result);
//        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
//        $queryBuilderMock->method('select')->willReturnSelf();
//        $queryBuilderMock->method('from')->willReturnSelf();
//        $queryBuilderMock->method('addOrderBy')->with('')->willReturnSelf();
//        $queryBuilderMock->method('setMaxResults')->willReturnSelf();
//        $queryBuilderMock->method('getQuery')->willReturn($queryMock);
//        $this->entityManagerMock->method('createQueryBuilder')->willReturn($queryBuilderMock);
//
//        $lastSupplierId = $this->supplierDataHandler->getLastSupplier();
//
//        self::assertIsInt($lastSupplierId);
//        self::assertEquals(5, $lastSupplierId);
//    }
}
