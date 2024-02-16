<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockRotationEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationTest
 */
#[CoversClass(StockRotationEntity::class)]
final class StockRotationTest extends TestCase
{
    private StockRotationEntity $stockRotationEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->stockRotationEntity = new StockRotationEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockRotationEntity->setId($id);
        self::assertEquals($id, $this->stockRotationEntity->getId());

        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockRotationEntity->setStockLocationId($stockLocationId);
        self::assertEquals($stockLocationId, $this->stockRotationEntity->getStockLocationId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->stockRotationEntity->setArticleId($articleId);
        self::assertEquals($articleId, $this->stockRotationEntity->getArticleId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->stockRotationEntity->setUsrId($usrId);
        self::assertEquals($usrId, $this->stockRotationEntity->getUsrId());

        // Test setCustomerOrderId() and getCustomerOrderId()
        $customerOrderId = 710000;
        $this->stockRotationEntity->setCustomerOrderId($customerOrderId);
        self::assertEquals($customerOrderId, $this->stockRotationEntity->getCustomerOrderId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 100000;
        $this->stockRotationEntity->setSupplierOrderId($supplierOrderId);
        self::assertEquals($supplierOrderId, $this->stockRotationEntity->getSupplierOrderId());

        // Test setMovementId() and getMovementId()
        $movementId = 1;
        $this->stockRotationEntity->setMovementId($movementId);
        self::assertEquals($movementId, $this->stockRotationEntity->getMovementId());

        // Test setPosQuantity() and getPosQuantity()
        $incomingStock = 150;
        $this->stockRotationEntity->setPosQuantity($incomingStock);
        self::assertEquals($incomingStock, $this->stockRotationEntity->getPosQuantity());

        // Test setAccessDate() and getAccessDate()
        $accessDate = $this->dateTime;
        $this->stockRotationEntity->setAccessDate($accessDate);
        self::assertEquals($accessDate, $this->stockRotationEntity->getAccessDate());

        // Test setDispatchDate() and getDispatchDate()
        $dispatchDate = $this->dateTime;
        $this->stockRotationEntity->setDispatchDate($dispatchDate);
        self::assertEquals($dispatchDate, $this->stockRotationEntity->getDispatchDate());

        // Test setTrType() and getTrType()
        $updatedAt = 'in';
        $this->stockRotationEntity->setTrType($updatedAt);
        self::assertEquals($updatedAt, $this->stockRotationEntity->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockRotationEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockRotationEntity->getCreatedAt());
    }
}
