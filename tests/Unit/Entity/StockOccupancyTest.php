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

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockOccupancy = new StockOccupancy();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockOccupancy);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('id');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('id');
        $this->stockOccupancy->setId($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetStockLocationId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('stockLocationId');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getStockLocationId());
    }

    public function testSetStockLocationId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('stockLocationId');
        $this->stockOccupancy->setStockLocationId($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('articleId');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getArticleId());
    }

    public function testSetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('articleId');
        $this->stockOccupancy->setArticleId($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetInStock(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('inStock');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getInStock());
    }

    public function testSetInStock(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('inStock');
        $this->stockOccupancy->setInStock($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetIncomingStock(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('incomingStock');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getIncomingStock());
    }

    public function testSetIncomingStock(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('incomingStock');
        $this->stockOccupancy->setIncomingStock($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetReservedStock(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('reservedStock');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getReservedStock());
    }

    public function testSetReservedStock(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('reservedStock');
        $this->stockOccupancy->setReservedStock($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('createdAt');
        $this->stockOccupancy->setCreatedAt($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('updatedAt');
        $property->setValue($this->stockOccupancy, $expected);
        self::assertSame($expected, $this->stockOccupancy->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockOccupancy::class))
            ->getProperty('updatedAt');
        $this->stockOccupancy->setUpdatedAt($expected);
        self::assertSame($expected, $property->getValue($this->stockOccupancy));
    }
}
