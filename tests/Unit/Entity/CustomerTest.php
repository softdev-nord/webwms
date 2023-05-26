<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    private ArrayCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = new Customer();
        $this->dateTime = new \DateTimeImmutable();
        $this->collection = new ArrayCollection();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setCustomerId() and getCustomerId()
        $customerId = 1;
        $this->customer->setCustomerId($customerId);
        self::assertEquals($customerId, $this->customer->getCustomerId());

        // Test setCustomerNr() and getCustomerNr()
        $customerNr = 12345;
        $this->customer->setCustomerNr($customerNr);
        self::assertEquals($customerNr, $this->customer->getCustomerNr());

        // Test setCustomerName() and getCustomerName()
        $customerName = 'Rene Irrgang';
        $this->customer->setCustomerName($customerName);
        self::assertEquals($customerName, $this->customer->getCustomerName());

        // Test setCustomerAddressAddition() and getCustomerAddressAddition()
        $customerAddressAddition = 'Adresszusatz';
        $this->customer->setCustomerAddressAddition($customerAddressAddition);
        self::assertEquals($customerAddressAddition, $this->customer->getCustomerAddressAddition());

        // Test setCustomerAddressStreet() and getCustomerAddressStreet()
        $customerAddressStreet = 'Spreenweg';
        $this->customer->setCustomerAddressStreet($customerAddressStreet);
        self::assertEquals($customerAddressStreet, $this->customer->getCustomerAddressStreet());

        // Test setCustomerAddressStreetNr() and getCustomerAddressStreetNr()
        $customerAddressStreetNr = '23a';
        $this->customer->setCustomerAddressStreetNr($customerAddressStreetNr);
        self::assertEquals($customerAddressStreetNr, $this->customer->getCustomerAddressStreetNr());

        // Test setCustomerCountryCode() and getCustomerCountryCode()
        $customerCountryCode = 'DE';
        $this->customer->setCustomerCountryCode($customerCountryCode);
        self::assertEquals($customerCountryCode, $this->customer->getCustomerCountryCode());

        // Test setCustomerZipCode() and getCustomerZipCode()
        $customerZipCode = '21698';
        $this->customer->setCustomerZipCode($customerZipCode);
        self::assertEquals($customerZipCode, $this->customer->getCustomerZipCode());

        // Test setCustomerCity() and getCustomerCity()
        $customerCity = 'Harsefeld';
        $this->customer->setCustomerCity($customerCity);
        self::assertEquals($customerCity, $this->customer->getCustomerCity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->customer->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->customer->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->customer->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->customer->getUpdatedAt());

        // Test setCustomerOrders() and getCustomerOrders()
        $customerOrder = $this->collection;
        $this->customer->setCustomerOrders($customerOrder);
        self::assertEquals($customerOrder, $this->customer->getCustomerOrders());
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

        self::assertEquals($expected, $this->customer->toArray());
    }
}
