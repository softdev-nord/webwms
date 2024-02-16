<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Entity\CustomerOrderPosEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosTest
 */
#[CoversClass(CustomerOrderPosEntity::class)]
final class CustomerOrderPosTest extends TestCase
{
    private CustomerOrderPosEntity $customerOrderPosEntity;

    private CustomerOrderEntity $customerOrderEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrderPosEntity = new CustomerOrderPosEntity();
        $this->customerOrderEntity = new CustomerOrderEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->customerOrderPosEntity->setId($id);
        self::assertEquals($id, $this->customerOrderPosEntity->getId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->customerOrderPosEntity->setCustomerOrderId($customerOrderId);
        self::assertEquals($customerOrderId, $this->customerOrderPosEntity->getCustomerOrderId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->customerOrderPosEntity->setArticleId($articleId);
        self::assertEquals($articleId, $this->customerOrderPosEntity->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->customerOrderPosEntity->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->customerOrderPosEntity->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test ArticleController Name';
        $this->customerOrderPosEntity->setArticleName($articleName);
        self::assertEquals($articleName, $this->customerOrderPosEntity->getArticleName());

        // Test setQuantity() and getQuantity()
        $quantity = 100;
        $this->customerOrderPosEntity->setQuantity($quantity);
        self::assertEquals($quantity, $this->customerOrderPosEntity->getQuantity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->customerOrderPosEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->customerOrderPosEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->customerOrderPosEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->customerOrderPosEntity->getUpdatedAt());

        // Test setCustomerOrder() and getCustomerOrder()
        $customerOrder = $this->customerOrderEntity;
        $this->customerOrderPosEntity->setCustomerOrder($customerOrder);
        self::assertEquals($customerOrder, $this->customerOrderPosEntity->getCustomerOrder());
    }
}
