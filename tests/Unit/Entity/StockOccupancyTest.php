<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockOccupancyEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyTest
 */
#[CoversClass(StockOccupancyEntity::class)]
final class StockOccupancyTest extends TestCase
{
    private StockOccupancyEntity $stockOccupancyEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->stockOccupancyEntity = new StockOccupancyEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockOccupancyEntity->setId($id);
        self::assertEquals($id, $this->stockOccupancyEntity->getId());

        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockOccupancyEntity->setStockLocationId($stockLocationId);
        self::assertEquals($stockLocationId, $this->stockOccupancyEntity->getStockLocationId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->stockOccupancyEntity->setArticleId($articleId);
        self::assertEquals($articleId, $this->stockOccupancyEntity->getArticleId());

        // Test setInStock() and getInStock()
        $inStock = 100;
        $this->stockOccupancyEntity->setInStock($inStock);
        self::assertEquals($inStock, $this->stockOccupancyEntity->getInStock());

        // Test setIncomingStock() and getIncomingStock()
        $incomingStock = 150;
        $this->stockOccupancyEntity->setIncomingStock($incomingStock);
        self::assertEquals($incomingStock, $this->stockOccupancyEntity->getIncomingStock());

        // Test setReservedStock() and getReservedStock()
        $reservedStock = 50;
        $this->stockOccupancyEntity->setReservedStock($reservedStock);
        self::assertEquals($reservedStock, $this->stockOccupancyEntity->getReservedStock());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockOccupancyEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockOccupancyEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockOccupancyEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockOccupancyEntity->getUpdatedAt());
    }
}
