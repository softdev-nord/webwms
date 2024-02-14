<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WebWMS\Entity\SupplierEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierTest
 */
#[CoversClass(SupplierEntity::class)]
final class SupplierTest extends TestCase
{
    private SupplierEntity $supplierEntity;

    private DateTimeImmutable $dateTimeImmutable;

    private ArrayCollection $collection;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierEntity = new SupplierEntity();
        $this->dateTimeImmutable = new DateTimeImmutable();
        $this->collection = new ArrayCollection();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setSupplierId() and getSupplierId()
        $supplierId = 1;
        $this->supplierEntity->setSupplierId($supplierId);
        self::assertEquals($supplierId, $this->supplierEntity->getSupplierId());

        // Test setSupplierNr() and getSupplierNr()
        $supplierNr = 12345;
        $this->supplierEntity->setSupplierNr($supplierNr);
        self::assertEquals($supplierNr, $this->supplierEntity->getSupplierNr());

        // Test setSupplierName() and getSupplierName()
        $supplierName = 'Rene Irrgang';
        $this->supplierEntity->setSupplierName($supplierName);
        self::assertEquals($supplierName, $this->supplierEntity->getSupplierName());

        // Test setSupplierAddressAddition() and getSupplierAddressAddition()
        $supplierAddressAddition = 'Adresszusatz';
        $this->supplierEntity->setSupplierAddressAddition($supplierAddressAddition);
        self::assertEquals($supplierAddressAddition, $this->supplierEntity->getSupplierAddressAddition());

        // Test setSupplierAddressStreet() and getSupplierAddressStreet()
        $supplierAddressStreet = 'Spreenweg';
        $this->supplierEntity->setSupplierAddressStreet($supplierAddressStreet);
        self::assertEquals($supplierAddressStreet, $this->supplierEntity->getSupplierAddressStreet());

        // Test setSupplierAddressStreetNr() and getSupplierAddressStreetNr()
        $supplierAddressStreetNr = '23a';
        $this->supplierEntity->setSupplierAddressStreetNr($supplierAddressStreetNr);
        self::assertEquals($supplierAddressStreetNr, $this->supplierEntity->getSupplierAddressStreetNr());

        // Test setSupplierAddressCountryCode() and getSupplierAddressCountryCode()
        $supplierAddressCountryCode = 'DE';
        $this->supplierEntity->setSupplierAddressCountryCode($supplierAddressCountryCode);
        self::assertEquals($supplierAddressCountryCode, $this->supplierEntity->getSupplierAddressCountryCode());

        // Test setSupplierAddressZipcode() and getSupplierAddressZipcode()
        $supplierAddressZipcode = '21698';
        $this->supplierEntity->setSupplierAddressZipcode($supplierAddressZipcode);
        self::assertEquals($supplierAddressZipcode, $this->supplierEntity->getSupplierAddressZipcode());

        // Test setSupplierAddressCity() and getSupplierAddressCity()
        $supplierAddressCity = 'Harsefeld';
        $this->supplierEntity->setSupplierAddressCity($supplierAddressCity);
        self::assertEquals($supplierAddressCity, $this->supplierEntity->getSupplierAddressCity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTimeImmutable;
        $this->supplierEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplierEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTimeImmutable;
        $this->supplierEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->supplierEntity->getUpdatedAt());

        // Test setSupplierOrders() and getSupplierOrders()
        $supplierOrder = $this->collection;
        $this->supplierEntity->setSupplierOrders($supplierOrder);
        self::assertEquals($supplierOrder, $this->supplierEntity->getSupplierOrders());
    }

    public function testToArray(): void
    {
        $this->supplierEntity->setSupplierId(1);
        $this->supplierEntity->setSupplierNr(12345);
        $this->supplierEntity->setSupplierName('Rene Irrgang');
        $this->supplierEntity->setSupplierAddressAddition('Adresszusatz');
        $this->supplierEntity->setSupplierAddressStreet('Spreenweg');
        $this->supplierEntity->setSupplierAddressStreetNr('23a');
        $this->supplierEntity->setSupplierAddressCountryCode('DE');
        $this->supplierEntity->setSupplierAddressZipcode('21698');
        $this->supplierEntity->setSupplierAddressCity('Harsefeld');

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

        self::assertEquals($expected, $this->supplierEntity->toArray());
        $reflectionClass = (new ReflectionClass(SupplierEntity::class));
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
