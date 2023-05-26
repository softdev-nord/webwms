<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Entity\SupplierOrderPos;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosTest
 *
 * @covers \WebWMS\Entity\SupplierOrderPos
 */
final class SupplierOrderPosTest extends TestCase
{
    private SupplierOrderPos $supplierOrderPos;

    private SupplierOrder $supplierOrder;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierOrderPos = new SupplierOrderPos();
        $this->supplierOrder = new SupplierOrder();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->supplierOrderPos->setId($id);
        self::assertEquals($id, $this->supplierOrderPos->getId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 710000;
        $this->supplierOrderPos->setSupplierOrderId($supplierOrderId);
        self::assertEquals($supplierOrderId, $this->supplierOrderPos->getSupplierOrderId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->supplierOrderPos->setArticleId($articleId);
        self::assertEquals($articleId, $this->supplierOrderPos->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->supplierOrderPos->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->supplierOrderPos->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test Article Name';
        $this->supplierOrderPos->setArticleName($articleName);
        self::assertEquals($articleName, $this->supplierOrderPos->getArticleName());

        // Test setSupplierOrderPosQuantity() and getSupplierOrderPosQuantity()
        $quantity = 100;
        $this->supplierOrderPos->setSupplierOrderPosQuantity($quantity);
        self::assertEquals($quantity, $this->supplierOrderPos->getSupplierOrderPosQuantity());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->supplierOrderPos->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplierOrderPos->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->supplierOrderPos->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->supplierOrderPos->getUpdatedAt());

        // Test setSupplierOrder() and getSupplierOrder()
        $supplierOrder = $this->supplierOrder;
        $this->supplierOrderPos->setSupplierOrder($supplierOrder);
        self::assertEquals($supplierOrder, $this->supplierOrderPos->getSupplierOrder());
    }
}
