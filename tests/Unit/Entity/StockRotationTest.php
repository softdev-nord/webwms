<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockRotation;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationTest
 *
 * @covers \WebWMS\Entity\StockRotation
 */
final class StockRotationTest extends TestCase
{
    private StockRotation $stockRotation;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockRotation = new StockRotation();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockRotation->setId($id);
        self::assertEquals($id, $this->stockRotation->getId());

        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockRotation->setStockLocationId($stockLocationId);
        self::assertEquals($stockLocationId, $this->stockRotation->getStockLocationId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->stockRotation->setArticleId($articleId);
        self::assertEquals($articleId, $this->stockRotation->getArticleId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->stockRotation->setUsrId($usrId);
        self::assertEquals($usrId, $this->stockRotation->getUsrId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->stockRotation->setCustomerOrderId($customerOrderId);
        self::assertEquals($customerOrderId, $this->stockRotation->getCustomerOrderId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 100000;
        $this->stockRotation->setSupplierOrderId($supplierOrderId);
        self::assertEquals($supplierOrderId, $this->stockRotation->getSupplierOrderId());

        // Test setMovementId() and getMovementId()
        $movementId = 1;
        $this->stockRotation->setMovementId($movementId);
        self::assertEquals($movementId, $this->stockRotation->getMovementId());

        // Test setPosQuantity() and getPosQuantity()
        $incomingStock = 150;
        $this->stockRotation->setPosQuantity($incomingStock);
        self::assertEquals($incomingStock, $this->stockRotation->getPosQuantity());

        // Test setAccessDate() and getAccessDate()
        $accessDate = $this->dateTime;
        $this->stockRotation->setAccessDate($accessDate);
        self::assertEquals($accessDate, $this->stockRotation->getAccessDate());

        // Test setDispatchDate() and getDispatchDate()
        $dispatchDate = $this->dateTime;
        $this->stockRotation->setDispatchDate($dispatchDate);
        self::assertEquals($dispatchDate, $this->stockRotation->getDispatchDate());

        // Test setTrType() and getTrType()
        $updatedAt = 'in';
        $this->stockRotation->setTrType($updatedAt);
        self::assertEquals($updatedAt, $this->stockRotation->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockRotation->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockRotation->getCreatedAt());
    }
}
