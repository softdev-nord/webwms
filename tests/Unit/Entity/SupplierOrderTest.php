<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Supplier;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierOrderTest'
)]
#[CoversClass(SupplierOrder::class)]
final class SupplierOrderTest extends TestCase
{
    private SupplierOrder $supplierOrder;

    private DateTime $dateTime;

    private ArrayCollection $collection;

    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierOrder = new SupplierOrder();
        $this->dateTime = new DateTime();
        $this->collection = new ArrayCollection();
        $this->supplier = new Supplier();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 710000;
        $this->supplierOrder->setId($id);
        self::assertSame($id, $this->supplierOrder->getId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 100000;
        $this->supplierOrder->setSupplierOrderId($supplierOrderId);
        self::assertSame($supplierOrderId, $this->supplierOrder->getSupplierOrderId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->supplierOrder->setUsrId($usrId);
        self::assertSame($usrId, $this->supplierOrder->getUsrId());

        // Test setSupplierId() and getSupplierId()
        $supplierId = 1;
        $this->supplierOrder->setSupplierId($supplierId);
        self::assertSame($supplierId, $this->supplierOrder->getSupplierId());

        // Test setSupplierOrderNr() and getSupplierOrderNr()
        $supplierOrderNr = 'EBE-01-100000';
        $this->supplierOrder->setSupplierOrderNr($supplierOrderNr);
        self::assertSame($supplierOrderNr, $this->supplierOrder->getSupplierOrderNr());

        // Test setSupplierOrderReference() and getSupplierOrderReference()
        $supplierOrderReference = 'Test Referenz';
        $this->supplierOrder->setSupplierOrderReference($supplierOrderReference);
        self::assertSame($supplierOrderReference, $this->supplierOrder->getSupplierOrderReference());

        // Test setSupplierOrderCreationDate() and getSupplierOrderCreationDate()
        $supplierOrderCreationDate = $this->dateTime;
        $this->supplierOrder->setSupplierOrderCreationDate($supplierOrderCreationDate);
        self::assertEquals($supplierOrderCreationDate, $this->supplierOrder->getSupplierOrderCreationDate());

        // Test setSupplierOrderDate() and getSupplierOrderDate()
        $supplierOrderDate = $this->dateTime;
        $this->supplierOrder->setSupplierOrderDate($supplierOrderDate);
        self::assertEquals($supplierOrderDate, $this->supplierOrder->getSupplierOrderDate());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->supplierOrder->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplierOrder->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->supplierOrder->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->supplierOrder->getUpdatedAt());

        // Test getSupplierOrderPos()
        $supplierOrderPos = $this->collection;
        self::assertEquals($supplierOrderPos, $this->supplierOrder->getSupplierOrderPos());

        // Test setSupplier() and getSupplier()
        $supplier = $this->supplier;
        $this->supplierOrder->setSupplier($supplier);
        self::assertEquals($supplier, $this->supplierOrder->getSupplier());
    }
}
