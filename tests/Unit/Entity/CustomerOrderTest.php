<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Entity\CustomerOrderEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        CustomerOrderTest
 */
#[CoversClass(CustomerOrderEntity::class)]
final class CustomerOrderTest extends TestCase
{
    private CustomerOrderEntity $customerOrderEntity;

    private DateTime $dateTime;

    private ArrayCollection $collection;

    private CustomerEntity $customerEntity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrderEntity = new CustomerOrderEntity();
        $this->dateTime = new DateTime();
        $this->collection = new ArrayCollection();
        $this->customerEntity = new CustomerEntity();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 710000;
        $this->customerOrderEntity->setId($id);
        self::assertEquals($id, $this->customerOrderEntity->getId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->customerOrderEntity->setCustomerOrderId($customerOrderId);
        self::assertEquals($customerOrderId, $this->customerOrderEntity->getCustomerOrderId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->customerOrderEntity->setUsrId($usrId);
        self::assertEquals($usrId, $this->customerOrderEntity->getUsrId());

        // Test setCustomerId() and getCustomerId()
        $customerId = 1;
        $this->customerOrderEntity->setCustomerId($customerId);
        self::assertEquals($customerId, $this->customerOrderEntity->getCustomerId());

        // Test setCustomerOrderNr() and getCustomerOrderNr()
        $customerOrderNr = 'VLS-01-710000';
        $this->customerOrderEntity->setCustomerOrderNr($customerOrderNr);
        self::assertEquals($customerOrderNr, $this->customerOrderEntity->getCustomerOrderNr());

        // Test setCustomerOrderReference() and getCustomerOrderReference()
        $customerOrderReference = 'Test Referenz';
        $this->customerOrderEntity->setCustomerOrderReference($customerOrderReference);
        self::assertEquals($customerOrderReference, $this->customerOrderEntity->getCustomerOrderReference());

        // Test setCustomerOrderCreationDate() and getCustomerOrderCreationDate()
        $customerOrderCreationDate = $this->dateTime;
        $this->customerOrderEntity->setCustomerOrderCreationDate($customerOrderCreationDate);
        self::assertEquals($customerOrderCreationDate, $this->customerOrderEntity->getCustomerOrderCreationDate());

        // Test setCustomerOrderDate() and getCustomerOrderDate()
        $customerOrderDate = $this->dateTime;
        $this->customerOrderEntity->setCustomerOrderDate($customerOrderDate);
        self::assertEquals($customerOrderDate, $this->customerOrderEntity->getCustomerOrderDate());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->customerOrderEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->customerOrderEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->customerOrderEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->customerOrderEntity->getUpdatedAt());

        // Test getCustomerOrderPos()
        $customerOrderPos = $this->collection;
        self::assertEquals($customerOrderPos, $this->customerOrderEntity->getCustomerOrderPos());

        // Test setCustomer() and getCustomer()
        $customer = $this->customerEntity;
        $this->customerOrderEntity->setCustomer($customer);
        self::assertEquals($customer, $this->customerOrderEntity->getCustomer());
    }
}
