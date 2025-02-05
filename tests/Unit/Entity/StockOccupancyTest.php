<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockOccupancy;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockOccupancyTest'
)]
#[CoversClass(StockOccupancy::class)]
final class StockOccupancyTest extends TestCase
{
    private StockOccupancy $stockOccupancy;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockOccupancy = new StockOccupancy();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockOccupancy->setId($id);
        self::assertSame($id, $this->stockOccupancy->getId());

        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockOccupancy->setStockLocationId($stockLocationId);
        self::assertSame($stockLocationId, $this->stockOccupancy->getStockLocationId());

        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->stockOccupancy->setArticleId($articleId);
        self::assertSame($articleId, $this->stockOccupancy->getArticleId());

        // Test setInStock() and getInStock()
        $inStock = 100;
        $this->stockOccupancy->setInStock($inStock);
        self::assertSame($inStock, $this->stockOccupancy->getInStock());

        // Test setIncomingStock() and getIncomingStock()
        $incomingStock = 150;
        $this->stockOccupancy->setIncomingStock($incomingStock);
        self::assertSame($incomingStock, $this->stockOccupancy->getIncomingStock());

        // Test setReservedStock() and getReservedStock()
        $reservedStock = 50;
        $this->stockOccupancy->setReservedStock($reservedStock);
        self::assertSame($reservedStock, $this->stockOccupancy->getReservedStock());

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
