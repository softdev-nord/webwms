<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Entity\SupplierOrderPosEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        SupplierOrderPosTest
 */
#[CoversClass(SupplierOrderPosEntity::class)]
final class SupplierOrderPosTest extends TestCase
{
    private SupplierOrderPosEntity $supplierOrderPosEntity;

    private SupplierOrderEntity $supplierOrderEntity;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierOrderPosEntity = new SupplierOrderPosEntity();
        $this->supplierOrderEntity = new SupplierOrderEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->supplierOrderPosEntity->setId($id);
        self::assertEquals($id, $this->supplierOrderPosEntity->getId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 710000;
        $this->supplierOrderPosEntity->setSupplierOrderId($supplierOrderId);
        self::assertEquals($supplierOrderId, $this->supplierOrderPosEntity->getSupplierOrderId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->supplierOrderPosEntity->setArticleId($articleId);
        self::assertEquals($articleId, $this->supplierOrderPosEntity->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->supplierOrderPosEntity->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->supplierOrderPosEntity->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test ArticleController Name';
        $this->supplierOrderPosEntity->setArticleName($articleName);
        self::assertEquals($articleName, $this->supplierOrderPosEntity->getArticleName());

        // Test setSupplierOrderPosQuantity() and getSupplierOrderPosQuantity()
        $quantity = 100;
        $this->supplierOrderPosEntity->setSupplierOrderPosQuantity($quantity);
        self::assertEquals($quantity, $this->supplierOrderPosEntity->getSupplierOrderPosQuantity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->supplierOrderPosEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplierOrderPosEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->supplierOrderPosEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->supplierOrderPosEntity->getUpdatedAt());

        // Test setSupplierOrder() and getSupplierOrder()
        $supplierOrder = $this->supplierOrderEntity;
        $this->supplierOrderPosEntity->setSupplierOrder($supplierOrder);
        self::assertEquals($supplierOrder, $this->supplierOrderPosEntity->getSupplierOrder());
    }
}
