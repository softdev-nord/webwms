<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierTest
 *
 * @covers \WebWMS\Entity\Supplier
 */
final class SupplierTest extends TestCase
{
    private Supplier $supplier;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplier = new Supplier();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->supplier);
        unset($this->dateTime);
    }

    public function testGetSupplierId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierId');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierId());
    }

    public function testSetSupplierId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierId');
        $this->supplier->setSupplierId($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierNr');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierNr());
    }

    public function testSetSupplierNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierNr');
        $this->supplier->setSupplierNr($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierName(): void
    {
        $expected = 'supplierName';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierName');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierName());
    }

    public function testSetSupplierName(): void
    {
        $expected = 'supplierName';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierName');
        $this->supplier->setSupplierName($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierAddressAddition(): void
    {
        $expected = 'supplierAddressAddition';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressAddition');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierAddressAddition());
    }

    public function testSetSupplierAddressAddition(): void
    {
        $expected = 'supplierAddressAddition';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressAddition');
        $this->supplier->setSupplierAddressAddition($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierAddressStreet(): void
    {
        $expected = 'supplierAddressStreet';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressStreet');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierAddressStreet());
    }

    public function testSetSupplierAddressStreet(): void
    {
        $expected = 'supplierAddressStreet';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressStreet');
        $this->supplier->setSupplierAddressStreet($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierAddressStreetNr(): void
    {
        $expected = 'supplierAddressStreetNr';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressStreetNr');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierAddressStreetNr());
    }

    public function testSetSupplierAddressStreetNr(): void
    {
        $expected = 'supplierAddressStreetNr';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressStreetNr');
        $this->supplier->setSupplierAddressStreetNr($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierAddressCountryCode(): void
    {
        $expected = 'supplierAddressCountryCode';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressCountryCode');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierAddressCountryCode());
    }

    public function testSetSupplierAddressCountryCode(): void
    {
        $expected = 'supplierAddressCountryCode';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressCountryCode');
        $this->supplier->setSupplierAddressCountryCode($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierAddressZipcode(): void
    {
        $expected = 'supplierAddressZipcode';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressZipcode');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierAddressZipcode());
    }

    public function testSetSupplierAddressZipcode(): void
    {
        $expected = 'supplierAddressZipcode';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressZipcode');
        $this->supplier->setSupplierAddressZipcode($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetSupplierAddressCity(): void
    {
        $expected = 'supplierAddressCity';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressCity');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getSupplierAddressCity());
    }

    public function testSetSupplierAddressCity(): void
    {
        $expected = 'supplierAddressCity';
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('supplierAddressCity');
        $this->supplier->setSupplierAddressCity($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testToArray(): void
    {
        $this->supplier->setSupplierId(1);
        $this->supplier->setSupplierNr(12345);
        $this->supplier->setSupplierName('Rene Irrgang');
        $this->supplier->setSupplierAddressAddition('Adresszusatz');
        $this->supplier->setSupplierAddressStreet('Spreenweg');
        $this->supplier->setSupplierAddressStreetNr('23a');
        $this->supplier->setSupplierAddressCountryCode('DE');
        $this->supplier->setSupplierAddressZipcode('21698');
        $this->supplier->setSupplierAddressCity('Harsefeld');

        $expected = [
            'supplierId' => 1,
            'supplierNr' => '12345',
            'supplierName' => 'Rene Irrgang',
            'supplierAddressAddition' => 'Adresszusatz',
            'supplierAddressStreet' => 'Spreenweg',
            'supplierAddressStreetNr' => '23a',
            'supplierAddressCountryCode' => 'DE',
            'supplierAddressZipcode' => '21698',
            'supplierAddressCity' => 'Harsefeld',
        ];

        $this->assertEquals($expected, $this->supplier->toArray());
        $property = (new \ReflectionClass(Supplier::class));
        $expected = [
            'supplierId' => $property->getProperty('supplierId'),
            'supplierNr' => $property->getProperty('supplierNr'),
            'supplierName' => $property->getProperty('supplierName'),
            'supplierAddressAddition' => $property->getProperty('supplierAddressAddition'),
            'supplierAddressStreet' => $property->getProperty('supplierAddressStreet'),
            'supplierAddressStreetNr' => $property->getProperty('supplierAddressStreetNr'),
            'supplierAddressCountryCode' => $property->getProperty('supplierAddressCountryCode'),
            'supplierAddressZipcode' => $property->getProperty('supplierAddressZipcode'),
            'supplierAddressCity' => $property->getProperty('supplierAddressCity'),
        ];
        $this->assertArrayHasKey('supplierId', $expected);
        $this->assertArrayHasKey('supplierNr', $expected);
        $this->assertArrayHasKey('supplierName', $expected);
        $this->assertArrayHasKey('supplierAddressAddition', $expected);
        $this->assertArrayHasKey('supplierAddressStreet', $expected);
        $this->assertArrayHasKey('supplierAddressStreetNr', $expected);
        $this->assertArrayHasKey('supplierAddressCountryCode', $expected);
        $this->assertArrayHasKey('supplierAddressZipcode', $expected);
        $this->assertArrayHasKey('supplierAddressCity', $expected);
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('createdAt');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('createdAt');
        $this->supplier->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('updatedAt');
        $property->setValue($this->supplier, $expected);
        $this->assertSame($expected, $this->supplier->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Supplier::class))
            ->getProperty('updatedAt');
        $this->supplier->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->supplier));
    }
}

