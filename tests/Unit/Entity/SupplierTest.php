<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WebWMS\Entity\Supplier;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierTest'
)]
#[CoversClass(Supplier::class)]
final class SupplierTest extends TestCase
{
    private Supplier $supplier;

    private DateTimeImmutable $dateTimeImmutable;

    private ArrayCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplier = new Supplier();
        $this->dateTimeImmutable = new DateTimeImmutable();
        $this->collection = new ArrayCollection();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setSupplierId() and getSupplierId()
        $supplierId = 1;
        $this->supplier->setSupplierId($supplierId);
        self::assertSame($supplierId, $this->supplier->getSupplierId());

        // Test setSupplierNr() and getSupplierNr()
        $supplierNr = 12345;
        $this->supplier->setSupplierNr($supplierNr);
        self::assertSame($supplierNr, $this->supplier->getSupplierNr());

        // Test setSupplierName() and getSupplierName()
        $supplierName = 'Rene Irrgang';
        $this->supplier->setSupplierName($supplierName);
        self::assertSame($supplierName, $this->supplier->getSupplierName());

        // Test setSupplierAddressAddition() and getSupplierAddressAddition()
        $supplierAddressAddition = 'Adresszusatz';
        $this->supplier->setSupplierAddressAddition($supplierAddressAddition);
        self::assertSame($supplierAddressAddition, $this->supplier->getSupplierAddressAddition());

        // Test setSupplierAddressStreet() and getSupplierAddressStreet()
        $supplierAddressStreet = 'Spreenweg';
        $this->supplier->setSupplierAddressStreet($supplierAddressStreet);
        self::assertSame($supplierAddressStreet, $this->supplier->getSupplierAddressStreet());

        // Test setSupplierAddressStreetNr() and getSupplierAddressStreetNr()
        $supplierAddressStreetNr = '23a';
        $this->supplier->setSupplierAddressStreetNr($supplierAddressStreetNr);
        self::assertSame($supplierAddressStreetNr, $this->supplier->getSupplierAddressStreetNr());

        // Test setSupplierAddressCountryCode() and getSupplierAddressCountryCode()
        $supplierAddressCountryCode = 'DE';
        $this->supplier->setSupplierAddressCountryCode($supplierAddressCountryCode);
        self::assertSame($supplierAddressCountryCode, $this->supplier->getSupplierAddressCountryCode());

        // Test setSupplierAddressZipcode() and getSupplierAddressZipcode()
        $supplierAddressZipcode = '21698';
        $this->supplier->setSupplierAddressZipcode($supplierAddressZipcode);
        self::assertSame($supplierAddressZipcode, $this->supplier->getSupplierAddressZipcode());

        // Test setSupplierAddressCity() and getSupplierAddressCity()
        $supplierAddressCity = 'Harsefeld';
        $this->supplier->setSupplierAddressCity($supplierAddressCity);
        self::assertSame($supplierAddressCity, $this->supplier->getSupplierAddressCity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTimeImmutable;
        $this->supplier->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplier->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTimeImmutable;
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

        self::assertSame($expected, $this->supplier->toArray());
        $reflectionClass = (new ReflectionClass(Supplier::class));
        $expected = [
            'supplierId' => $reflectionClass->getProperty('supplierId'),
            'supplierNr' => $reflectionClass->getProperty('supplierNr'),
            'supplierName' => $reflectionClass->getProperty('supplierName'),
            'supplierAddressAddition' => $reflectionClass->getProperty('supplierAddressAddition'),
            'supplierAddressStreet' => $reflectionClass->getProperty('supplierAddressStreet'),
            'supplierAddressStreetNr' => $reflectionClass->getProperty('supplierAddressStreetNr'),
            'supplierAddressCountryCode' => $reflectionClass->getProperty('supplierAddressCountryCode'),
            'supplierAddressZipcode' => $reflectionClass->getProperty('supplierAddressZipcode'),
            'supplierAddressCity' => $reflectionClass->getProperty('supplierAddressCity'),
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
