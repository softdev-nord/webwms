<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use WebWMS\Entity\StockLayout;
use PHPUnit\Framework\TestCase;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutTest
 *
 * @covers \WebWMS\Entity\StockLayout
 */
final class StockLayoutTest extends TestCase
{
    private StockLayout $stockLayout;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLayout = new StockLayout();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockLayout);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('id');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('id');
        $this->stockLayout->setId($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockNr');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockNr());
    }

    public function testSetStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockNr');
        $this->stockLayout->setStockNr($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockDescription(): void
    {
        $expected = 'stockDescription';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockDescription');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockDescription());
    }

    public function testSetStockDescription(): void
    {
        $expected = 'stockDescription';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockDescription');
        $this->stockLayout->setStockDescription($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel1');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockLevel1());
    }

    public function testSetStockLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel1');
        $this->stockLayout->setStockLevel1($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel2');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockLevel2());
    }

    public function testSetStockLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel2');
        $this->stockLayout->setStockLevel2($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel3');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockLevel3());
    }

    public function testSetStockLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel3');
        $this->stockLayout->setStockLevel3($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel4');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockLevel4());
    }

    public function testSetStockLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLevel4');
        $this->stockLayout->setStockLevel4($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockModel(): void
    {
        $expected = 'stockModel';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockModel');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockModel());
    }

    public function testSetStockModel(): void
    {
        $expected = 'stockModel';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockModel');
        $this->stockLayout->setStockModel($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockTyp(): void
    {
        $expected = 'stockTyp';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockTyp');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockTyp());
    }

    public function testSetStockTyp(): void
    {
        $expected = 'stockTyp';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockTyp');
        $this->stockLayout->setStockTyp($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetStockLongDescription(): void
    {
        $expected = 'stockLongDescription';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLongDescription');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getStockLongDescription());
    }

    public function testSetStockLongDescription(): void
    {
        $expected = 'stockLongDescription';
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('stockLongDescription');
        $this->stockLayout->setStockLongDescription($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('createdAt');
        $this->stockLayout->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('updatedAt');
        $property->setValue($this->stockLayout, $expected);
        $this->assertSame($expected, $this->stockLayout->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLayout::class))
            ->getProperty('updatedAt');
        $this->stockLayout->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockLayout));
    }
}
