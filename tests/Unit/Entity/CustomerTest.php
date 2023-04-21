<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Customer;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerTest
 *
 * @covers \WebWMS\Entity\Customer
 */
final class CustomerTest extends TestCase
{
    private Customer $customer;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = new Customer();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->customer);
        unset($this->dateTime);
    }

    public function testGetCustomerId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerId');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerId());
    }

    public function testSetCustomerId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerId');
        $this->customer->setCustomerId($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerNr');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerNr());
    }

    public function testSetCustomerNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerNr');
        $this->customer->setCustomerNr($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerName(): void
    {
        $expected = 'customerName';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerName');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerName());
    }

    public function testSetCustomerName(): void
    {
        $expected = 'customerName';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerName');
        $this->customer->setCustomerName($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerAddressAddition(): void
    {
        $expected = 'customerAddressAddition';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerAddressAddition');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerAddressAddition());
    }

    public function testSetCustomerAddressAddition(): void
    {
        $expected = 'customerAddressAddition';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerAddressAddition');
        $this->customer->setCustomerAddressAddition($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerAddressStreet(): void
    {
        $expected = 'customerAddressStreet';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerAddressStreet');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerAddressStreet());
    }

    public function testSetCustomerAddressStreet(): void
    {
        $expected = 'customerAddressStreet';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerAddressStreet');
        $this->customer->setCustomerAddressStreet($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerAddressStreetNr(): void
    {
        $expected = 'customerAddressStreetNr';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerAddressStreetNr');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerAddressStreetNr());
    }

    public function testSetCustomerAddressStreetNr(): void
    {
        $expected = 'customerAddressStreetNr';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerAddressStreetNr');
        $this->customer->setCustomerAddressStreetNr($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerCountryCode(): void
    {
        $expected = 'customerCountryCode';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerCountryCode');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerCountryCode());
    }

    public function testSetCustomerCountryCode(): void
    {
        $expected = 'customerCountryCode';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerCountryCode');
        $this->customer->setCustomerCountryCode($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerZipCode(): void
    {
        $expected = 'customerZipCode';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerZipCode');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerZipCode());
    }

    public function testSetCustomerZipCode(): void
    {
        $expected = 'customerZipCode';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerZipCode');
        $this->customer->setCustomerZipCode($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetCustomerCity(): void
    {
        $expected = 'customerCity';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerCity');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCustomerCity());
    }

    public function testSetCustomerCity(): void
    {
        $expected = 'customerCity';
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('customerCity');
        $this->customer->setCustomerCity($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testToArray(): void
    {
        $this->customer->setCustomerId(1);
        $this->customer->setCustomerNr(12345);
        $this->customer->setCustomerName('Rene Irrgang');
        $this->customer->setCustomerAddressAddition('Adresszusatz');
        $this->customer->setCustomerAddressStreet('Spreenweg');
        $this->customer->setCustomerAddressStreetNr('23a');
        $this->customer->setCustomerCountryCode('DE');
        $this->customer->setCustomerZipCode('21698');
        $this->customer->setCustomerCity('Harsefeld');

        $expected = [
            'customerId' => 1,
            'customerNr' => '12345',
            'customerName' => 'Rene Irrgang',
            'customerAddressAddition' => 'Adresszusatz',
            'customerAddressStreet' => 'Spreenweg',
            'customerAddressStreetNr' => '23a',
            'customerCountryCode' => 'DE',
            'customerZipCode' => '21698',
            'customerCity' => 'Harsefeld',
        ];

        $this->assertEquals($expected, $this->customer->toArray());
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('createdAt');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('createdAt');
        $this->customer->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('updatedAt');
        $property->setValue($this->customer, $expected);
        $this->assertSame($expected, $this->customer->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Customer::class))
            ->getProperty('updatedAt');
        $this->customer->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->customer));
    }
}
