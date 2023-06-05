<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosTest
 *
 * @covers \WebWMS\Entity\CustomerOrderPos
 */
final class CustomerOrderPosTest extends TestCase
{
    private CustomerOrderPos $customerOrderPos;

    private CustomerOrder $customerOrder;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrderPos = new CustomerOrderPos();
        $this->customerOrder = new CustomerOrder();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->customerOrderPos->setId($id);
        self::assertEquals($id, $this->customerOrderPos->getId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->customerOrderPos->setCustomerOrderId($customerOrderId);
        self::assertEquals($customerOrderId, $this->customerOrderPos->getCustomerOrderId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->customerOrderPos->setArticleId($articleId);
        self::assertEquals($articleId, $this->customerOrderPos->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->customerOrderPos->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->customerOrderPos->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test Article Name';
        $this->customerOrderPos->setArticleName($articleName);
        self::assertEquals($articleName, $this->customerOrderPos->getArticleName());

        // Test setQuantity() and getQuantity()
        $quantity = 100;
        $this->customerOrderPos->setQuantity($quantity);
        self::assertEquals($quantity, $this->customerOrderPos->getQuantity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->customerOrderPos->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->customerOrderPos->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->customerOrderPos->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->customerOrderPos->getUpdatedAt());

        // Test setCustomerOrder() and getCustomerOrder()
        $customerOrder = $this->customerOrder;
        $this->customerOrderPos->setCustomerOrder($customerOrder);
        self::assertEquals($customerOrder, $this->customerOrderPos->getCustomerOrder());
    }
}
