<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Customer;
use WebWMS\Entity\CustomerOrder;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderTest
 *
 * @covers \WebWMS\Entity\CustomerOrder
 */
final class CustomerOrderTest extends TestCase
{
    private CustomerOrder $customerOrder;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrder = new CustomerOrder();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->customerOrder);
        unset($this->dateTime);
    }

    public function testGetCustomer(): void
    {
        $expected = $this->createMock(Customer::class);
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customer');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomer());
    }

    public function testSetCustomer(): void
    {
        $expected = $this->createMock(Customer::class);
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customer');
        $this->customerOrder->setCustomer($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('id');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('id');
        $this->customerOrder->setId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetUsrId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('usrId');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getUsrId());
    }

    public function testSetUsrId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('usrId');
        $this->customerOrder->setUsrId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerId');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerId());
    }

    public function testSetCustomerId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerId');
        $this->customerOrder->setCustomerId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderId');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerOrderId());
    }

    public function testSetCustomerOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderId');
        $this->customerOrder->setCustomerOrderId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerOrderNr(): void
    {
        $expected = 'customerOrderNr';
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderNr');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerOrderNr());
    }

    public function testSetCustomerOrderNr(): void
    {
        $expected = 'customerOrderNr';
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderNr');
        $this->customerOrder->setCustomerOrderNr($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerOrderReference(): void
    {
        $expected = 'customerOrderReference';
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderReference');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerOrderReference());
    }

    public function testSetCustomerOrderReference(): void
    {
        $expected = 'customerOrderReference';
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderReference');
        $this->customerOrder->setCustomerOrderReference($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerOrderDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderDate');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerOrderDate());
    }

    public function testSetCustomerOrderDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderDate');
        $this->customerOrder->setCustomerOrderDate($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerOrderCreationDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderCreationDate');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerOrderCreationDate());
    }

    public function testSetCustomerOrderCreationDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderCreationDate');
        $this->customerOrder->setCustomerOrderCreationDate($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetCustomerOrderPos(): void
    {
        $expected = $this->createMock(Collection::class);
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('customerOrderPos');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCustomerOrderPos());
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('createdAt');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('createdAt');
        $this->customerOrder->setCreatedAt($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('updatedAt');
        $property->setValue($this->customerOrder, $expected);
        self::assertSame($expected, $this->customerOrder->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrder::class))
            ->getProperty('updatedAt');
        $this->customerOrder->setUpdatedAt($expected);
        self::assertSame($expected, $property->getValue($this->customerOrder));
    }
}
