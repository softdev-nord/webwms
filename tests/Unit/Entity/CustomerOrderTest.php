<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    private \DateTime $dateTime;

    private ArrayCollection $collection;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrder = new CustomerOrder();
        $this->dateTime = new \DateTime();
        $this->collection = new ArrayCollection();
        $this->customer = new Customer();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 710000;
        $this->customerOrder->setId($id);
        self::assertEquals($id, $this->customerOrder->getId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->customerOrder->setCustomerOrderId($customerOrderId);
        self::assertEquals($customerOrderId, $this->customerOrder->getCustomerOrderId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->customerOrder->setUsrId($usrId);
        self::assertEquals($usrId, $this->customerOrder->getUsrId());

        // Test setCustomerId() and getCustomerId()
        $customerId = 1;
        $this->customerOrder->setCustomerId($customerId);
        self::assertEquals($customerId, $this->customerOrder->getCustomerId());

        // Test setCustomerOrderNr() and getCustomerOrderNr()
        $customerOrderNr = 'VLS-01-710000';
        $this->customerOrder->setCustomerOrderNr($customerOrderNr);
        self::assertEquals($customerOrderNr, $this->customerOrder->getCustomerOrderNr());

        // Test setCustomerOrderReference() and getCustomerOrderReference()
        $customerOrderReference = 'Test Referenz';
        $this->customerOrder->setCustomerOrderReference($customerOrderReference);
        self::assertEquals($customerOrderReference, $this->customerOrder->getCustomerOrderReference());

        // Test setCustomerOrderCreationDate() and getCustomerOrderCreationDate()
        $customerOrderCreationDate = $this->dateTime;
        $this->customerOrder->setCustomerOrderCreationDate($customerOrderCreationDate);
        self::assertEquals($customerOrderCreationDate, $this->customerOrder->getCustomerOrderCreationDate());

        // Test setCustomerOrderDate() and getCustomerOrderDate()
        $customerOrderDate = $this->dateTime;
        $this->customerOrder->setCustomerOrderDate($customerOrderDate);
        self::assertEquals($customerOrderDate, $this->customerOrder->getCustomerOrderDate());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->customerOrder->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->customerOrder->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->customerOrder->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->customerOrder->getUpdatedAt());

        // Test getCustomerOrderPos()
        $customerOrderPos = $this->collection;
        self::assertEquals($customerOrderPos, $this->customerOrder->getCustomerOrderPos());

        // Test setCustomer() and getCustomer()
        $customer = $this->customer;
        $this->customerOrder->setCustomer($customer);
        self::assertEquals($customer, $this->customerOrder->getCustomer());
    }
}
