<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockOccupancy;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyTest
 *
 * @covers \WebWMS\Entity\StockOccupancy
 */
final class StockOccupancyTest extends TestCase
{
    private StockOccupancy $stockOccupancy;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockOccupancy = new StockOccupancy();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockOccupancy->setId($id);
        self::assertEquals($id, $this->stockOccupancy->getId());

        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockOccupancy->setStockLocationId($stockLocationId);
        self::assertEquals($stockLocationId, $this->stockOccupancy->getStockLocationId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->stockOccupancy->setArticleId($articleId);
        self::assertEquals($articleId, $this->stockOccupancy->getArticleId());

        // Test setInStock() and getInStock()
        $inStock = 100;
        $this->stockOccupancy->setInStock($inStock);
        self::assertEquals($inStock, $this->stockOccupancy->getInStock());

        // Test setIncomingStock() and getIncomingStock()
        $incomingStock = 150;
        $this->stockOccupancy->setIncomingStock($incomingStock);
        self::assertEquals($incomingStock, $this->stockOccupancy->getIncomingStock());

        // Test setReservedStock() and getReservedStock()
        $reservedStock = 50;
        $this->stockOccupancy->setReservedStock($reservedStock);
        self::assertEquals($reservedStock, $this->stockOccupancy->getReservedStock());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockOccupancy->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockOccupancy->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockOccupancy->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockOccupancy->getUpdatedAt());
    }
}
