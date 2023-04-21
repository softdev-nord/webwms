<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockZoneLayout;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneLayoutTest
 *
 * @covers \WebWMS\Entity\StockZoneLayout
 */
final class StockZoneLayoutTest extends TestCase
{
    private StockZoneLayout $stockZoneLayout;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockZoneLayout = new StockZoneLayout();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockZoneLayout);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $this->stockZoneLayout->setId($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetStockZoneShortDesc(): void
    {
        $expected = 'stockZoneShortDesc';
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('stockZoneShortDesc');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getStockZoneShortDesc());
    }

    public function testSetZoneShortDesc(): void
    {
        $expected = 'stockZoneShortDesc';
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('stockZoneShortDesc');
        $this->stockZoneLayout->setZoneShortDesc($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetFromCoordinate(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromCoordinate');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getFromCoordinate());
    }

    public function testSetFromCoordinate(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromCoordinate');
        $this->stockZoneLayout->setFromCoordinate($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetFromStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromStockNr');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getFromStockNr());
    }

    public function testSetFromStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromStockNr');
        $this->stockZoneLayout->setFromStockNr($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetFromLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel1');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getFromLevel1());
    }

    public function testSetFromLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel1');
        $this->stockZoneLayout->setFromLevel1($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetFromLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel2');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getFromLevel2());
    }

    public function testSetFromLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel2');
        $this->stockZoneLayout->setFromLevel2($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetFromLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel3');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getFromLevel3());
    }

    public function testSetFromLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel3');
        $this->stockZoneLayout->setFromLevel3($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetFromLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel4');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getFromLevel4());
    }

    public function testSetFromLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('fromLevel4');
        $this->stockZoneLayout->setFromLevel4($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetToCoordinate(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toCoordinate');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getToCoordinate());
    }

    public function testSetToCoordinate(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toCoordinate');
        $this->stockZoneLayout->setToCoordinate($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetToLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel1');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getToLevel1());
    }

    public function testSetToLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel1');
        $this->stockZoneLayout->setToLevel1($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetToLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel2');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getToLevel2());
    }

    public function testSetToLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel2');
        $this->stockZoneLayout->setToLevel2($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetToLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel3');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getToLevel3());
    }

    public function testSetToLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel3');
        $this->stockZoneLayout->setToLevel3($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetToLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel4');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getToLevel4());
    }

    public function testSetToLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('toLevel4');
        $this->stockZoneLayout->setToLevel4($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetSumStockLoc(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('sumStockLoc');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getSumStockLoc());
    }

    public function testSetSumStockLoc(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('sumStockLoc');
        $this->stockZoneLayout->setSumStockLoc($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('createdAt');
        $this->stockZoneLayout->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('updatedAt');
        $property->setValue($this->stockZoneLayout, $expected);
        $this->assertSame($expected, $this->stockZoneLayout->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZoneLayout::class))
            ->getProperty('updatedAt');
        $this->stockZoneLayout->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockZoneLayout));
    }
}
