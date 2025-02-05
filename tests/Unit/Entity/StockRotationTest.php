<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockRotation;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockRotationTest'
)]
#[CoversClass(StockRotation::class)]
final class StockRotationTest extends TestCase
{
    private StockRotation $stockRotation;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockRotation = new StockRotation();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockRotation->setId($id);
        self::assertSame($id, $this->stockRotation->getId());

        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockRotation->setStockLocationId($stockLocationId);
        self::assertSame($stockLocationId, $this->stockRotation->getStockLocationId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->stockRotation->setArticleId($articleId);
        self::assertSame($articleId, $this->stockRotation->getArticleId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->stockRotation->setUsrId($usrId);
        self::assertSame($usrId, $this->stockRotation->getUsrId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->stockRotation->setCustomerOrderId($customerOrderId);
        self::assertSame($customerOrderId, $this->stockRotation->getCustomerOrderId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 100000;
        $this->stockRotation->setSupplierOrderId($supplierOrderId);
        self::assertSame($supplierOrderId, $this->stockRotation->getSupplierOrderId());

        // Test setMovementId() and getMovementId()
        $movementId = 1;
        $this->stockRotation->setMovementId($movementId);
        self::assertSame($movementId, $this->stockRotation->getMovementId());

        // Test setPosQuantity() and getPosQuantity()
        $incomingStock = 150;
        $this->stockRotation->setPosQuantity($incomingStock);
        self::assertSame($incomingStock, $this->stockRotation->getPosQuantity());

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
        self::assertSame($updatedAt, $this->stockRotation->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockRotation->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockRotation->getCreatedAt());
    }
}
