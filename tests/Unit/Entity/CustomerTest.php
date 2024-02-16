<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\CustomerEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerTest
 */
#[CoversClass(CustomerEntity::class)]
final class CustomerTest extends TestCase
{
    private CustomerEntity $customerEntity;

    private DateTimeImmutable $dateTimeImmutable;

    private ArrayCollection $collection;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->customerEntity = new CustomerEntity();
        $this->dateTimeImmutable = new DateTimeImmutable();
        $this->collection = new ArrayCollection();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setCustomerId() and getCustomerId()
        $customerId = 1;
        $this->customerEntity->setCustomerId($customerId);
        self::assertEquals($customerId, $this->customerEntity->getCustomerId());

        // Test setCustomerNr() and getCustomerNr()
        $customerNr = 12345;
        $this->customerEntity->setCustomerNr($customerNr);
        self::assertEquals($customerNr, $this->customerEntity->getCustomerNr());

        // Test setCustomerName() and getCustomerName()
        $customerName = 'Rene Irrgang';
        $this->customerEntity->setCustomerName($customerName);
        self::assertEquals($customerName, $this->customerEntity->getCustomerName());

        // Test setCustomerAddressAddition() and getCustomerAddressAddition()
        $customerAddressAddition = 'Adresszusatz';
        $this->customerEntity->setCustomerAddressAddition($customerAddressAddition);
        self::assertEquals($customerAddressAddition, $this->customerEntity->getCustomerAddressAddition());

        // Test setCustomerAddressStreet() and getCustomerAddressStreet()
        $customerAddressStreet = 'Spreenweg';
        $this->customerEntity->setCustomerAddressStreet($customerAddressStreet);
        self::assertEquals($customerAddressStreet, $this->customerEntity->getCustomerAddressStreet());

        // Test setCustomerAddressStreetNr() and getCustomerAddressStreetNr()
        $customerAddressStreetNr = '23a';
        $this->customerEntity->setCustomerAddressStreetNr($customerAddressStreetNr);
        self::assertEquals($customerAddressStreetNr, $this->customerEntity->getCustomerAddressStreetNr());

        // Test setCustomerCountryCode() and getCustomerCountryCode()
        $customerCountryCode = 'DE';
        $this->customerEntity->setCustomerCountryCode($customerCountryCode);
        self::assertEquals($customerCountryCode, $this->customerEntity->getCustomerCountryCode());

        // Test setCustomerZipCode() and getCustomerZipCode()
        $customerZipCode = '21698';
        $this->customerEntity->setCustomerZipCode($customerZipCode);
        self::assertEquals($customerZipCode, $this->customerEntity->getCustomerZipCode());

        // Test setCustomerCity() and getCustomerCity()
        $customerCity = 'Harsefeld';
        $this->customerEntity->setCustomerCity($customerCity);
        self::assertEquals($customerCity, $this->customerEntity->getCustomerCity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTimeImmutable;
        $this->customerEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->customerEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTimeImmutable;
        $this->customerEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->customerEntity->getUpdatedAt());

        // Test setCustomerOrders() and getCustomerOrders()
        $customerOrder = $this->collection;
        $this->customerEntity->setCustomerOrders($customerOrder);
        self::assertEquals($customerOrder, $this->customerEntity->getCustomerOrders());
    }

    public function testToArray(): void
    {
        $this->customerEntity->setCustomerId(1);
        $this->customerEntity->setCustomerNr(12345);
        $this->customerEntity->setCustomerName('Rene Irrgang');
        $this->customerEntity->setCustomerAddressAddition('Adresszusatz');
        $this->customerEntity->setCustomerAddressStreet('Spreenweg');
        $this->customerEntity->setCustomerAddressStreetNr('23a');
        $this->customerEntity->setCustomerCountryCode('DE');
        $this->customerEntity->setCustomerZipCode('21698');
        $this->customerEntity->setCustomerCity('Harsefeld');

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

        self::assertEquals($expected, $this->customerEntity->toArray());
    }
}
