<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    private ArrayCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplier = new Supplier();
        $this->dateTime = new \DateTimeImmutable();
        $this->collection = new ArrayCollection();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setSupplierId() and getSupplierId()
        $supplierId = 1;
        $this->supplier->setSupplierId($supplierId);
        self::assertEquals($supplierId, $this->supplier->getSupplierId());

        // Test setSupplierNr() and getSupplierNr()
        $supplierNr = 12345;
        $this->supplier->setSupplierNr($supplierNr);
        self::assertEquals($supplierNr, $this->supplier->getSupplierNr());

        // Test setSupplierName() and getSupplierName()
        $supplierName = 'Rene Irrgang';
        $this->supplier->setSupplierName($supplierName);
        self::assertEquals($supplierName, $this->supplier->getSupplierName());

        // Test setSupplierAddressAddition() and getSupplierAddressAddition()
        $supplierAddressAddition = 'Adresszusatz';
        $this->supplier->setSupplierAddressAddition($supplierAddressAddition);
        self::assertEquals($supplierAddressAddition, $this->supplier->getSupplierAddressAddition());

        // Test setSupplierAddressStreet() and getSupplierAddressStreet()
        $supplierAddressStreet = 'Spreenweg';
        $this->supplier->setSupplierAddressStreet($supplierAddressStreet);
        self::assertEquals($supplierAddressStreet, $this->supplier->getSupplierAddressStreet());

        // Test setSupplierAddressStreetNr() and getSupplierAddressStreetNr()
        $supplierAddressStreetNr = '23a';
        $this->supplier->setSupplierAddressStreetNr($supplierAddressStreetNr);
        self::assertEquals($supplierAddressStreetNr, $this->supplier->getSupplierAddressStreetNr());

        // Test setSupplierAddressCountryCode() and getSupplierAddressCountryCode()
        $supplierAddressCountryCode = 'DE';
        $this->supplier->setSupplierAddressCountryCode($supplierAddressCountryCode);
        self::assertEquals($supplierAddressCountryCode, $this->supplier->getSupplierAddressCountryCode());

        // Test setSupplierAddressZipcode() and getSupplierAddressZipcode()
        $supplierAddressZipcode = '21698';
        $this->supplier->setSupplierAddressZipcode($supplierAddressZipcode);
        self::assertEquals($supplierAddressZipcode, $this->supplier->getSupplierAddressZipcode());

        // Test setSupplierAddressCity() and getSupplierAddressCity()
        $supplierAddressCity = 'Harsefeld';
        $this->supplier->setSupplierAddressCity($supplierAddressCity);
        self::assertEquals($supplierAddressCity, $this->supplier->getSupplierAddressCity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->supplier->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplier->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->supplier->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->supplier->getUpdatedAt());

        // Test setSupplierOrders() and getSupplierOrders()
        $supplierOrder = $this->collection;
        $this->supplier->setSupplierOrders($supplierOrder);
        self::assertEquals($supplierOrder, $this->supplier->getSupplierOrders());
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

        self::assertEquals($expected, $this->supplier->toArray());
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
        self::assertArrayHasKey('supplierId', $expected);
        self::assertArrayHasKey('supplierNr', $expected);
        self::assertArrayHasKey('supplierName', $expected);
        self::assertArrayHasKey('supplierAddressAddition', $expected);
        self::assertArrayHasKey('supplierAddressStreet', $expected);
        self::assertArrayHasKey('supplierAddressStreetNr', $expected);
        self::assertArrayHasKey('supplierAddressCountryCode', $expected);
        self::assertArrayHasKey('supplierAddressZipcode', $expected);
        self::assertArrayHasKey('supplierAddressCity', $expected);
    }
}
