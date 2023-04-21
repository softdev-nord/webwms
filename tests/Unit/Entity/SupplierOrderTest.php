<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Supplier;
use WebWMS\Entity\SupplierOrder;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderTest
 *
 * @covers \WebWMS\Entity\SupplierOrder
 */
final class SupplierOrderTest extends TestCase
{
    private SupplierOrder $supplierOrder;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierOrder = new SupplierOrder();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->supplierOrder);
        unset($this->dateTime);
    }

    public function testGetSupplier(): void
    {
        $expected = $this->createMock(Supplier::class);
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplier');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplier());
    }

    public function testSetSupplier(): void
    {
        $expected = $this->createMock(Supplier::class);
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplier');
        $this->supplierOrder->setSupplier($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('id');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('id');
        $this->supplierOrder->setId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetUsrId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('usrId');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getUsrId());
    }

    public function testSetUsrId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('usrId');
        $this->supplierOrder->setUsrId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierId');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierId());
    }

    public function testSetSupplierId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierId');
        $this->supplierOrder->setSupplierId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderId');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierOrderId());
    }

    public function testSetSupplierOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderId');
        $this->supplierOrder->setSupplierOrderId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierOrderNr(): void
    {
        $expected = 'supplierOrderNr';
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderNr');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierOrderNr());
    }

    public function testSetSupplierOrderNr(): void
    {
        $expected = 'supplierOrderNr';
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderNr');
        $this->supplierOrder->setSupplierOrderNr($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierOrderReference(): void
    {
        $expected = 'supplierOrderReference';
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderReference');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierOrderReference());
    }

    public function testSetSupplierOrderReference(): void
    {
        $expected = 'supplierOrderReference';
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderReference');
        $this->supplierOrder->setSupplierOrderReference($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierOrderDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderDate');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierOrderDate());
    }

    public function testSetSupplierOrderDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderDate');
        $this->supplierOrder->setSupplierOrderDate($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierOrderCreationDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderCreationDate');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierOrderCreationDate());
    }

    public function testSetSupplierOrderCreationDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderCreationDate');
        $this->supplierOrder->setSupplierOrderCreationDate($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetSupplierOrderPos(): void
    {
        $expected = $this->createMock(Collection::class);
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('supplierOrderPos');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getSupplierOrderPos());
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('createdAt');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('createdAt');
        $this->supplierOrder->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('updatedAt');
        $property->setValue($this->supplierOrder, $expected);
        $this->assertSame($expected, $this->supplierOrder->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrder::class))
            ->getProperty('updatedAt');
        $this->supplierOrder->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrder));
    }
}
