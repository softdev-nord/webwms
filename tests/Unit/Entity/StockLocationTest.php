<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationTest
 *
 * @covers \WebWMS\Entity\StockLocation
 */
final class StockLocationTest extends TestCase
{
    private StockLocation $stockLocation;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLocation = new StockLocation();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockLocation);
        unset($this->dateTime);
    }

    public function testGetStockLocationId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationId');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationId());
    }

    public function testSetStockLocationId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationId');
        $this->stockLocation->setStockLocationId($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationLn(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationLn');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationLn());
    }

    public function testSetStockLocationLn(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationLn');
        $this->stockLocation->setStockLocationLn($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationFb(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationFb');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationFb());
    }

    public function testSetStockLocationFb(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationFb');
        $this->stockLocation->setStockLocationFb($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationSp(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationSp');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationSp());
    }

    public function testSetStockLocationSp(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationSp');
        $this->stockLocation->setStockLocationSp($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationTf(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationTf');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationTf());
    }

    public function testSetStockLocationTf(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationTf');
        $this->stockLocation->setStockLocationTf($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationCoordinate(): void
    {
        $expected = 'stockLocationCoordinate';
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationCoordinate');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationCoordinate());
    }

    public function testSetStockLocationCoordinate(): void
    {
        $expected = 'stockLocationCoordinate';
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationCoordinate');
        $this->stockLocation->setStockLocationCoordinate($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationDesc(): void
    {
        $expected = 'stockLocationDesc';
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationDesc');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationDesc());
    }

    public function testSetStockLocationDesc(): void
    {
        $expected = 'stockLocationDesc';
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationDesc');
        $this->stockLocation->setStockLocationDesc($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationWidth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationWidth');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationWidth());
    }

    public function testSetStockLocationWidth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationWidth');
        $this->stockLocation->setStockLocationWidth($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationDepth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationDepth');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationDepth());
    }

    public function testSetStockLocationDepth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationDepth');
        $this->stockLocation->setStockLocationDepth($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationHeight(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationHeight');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationHeight());
    }

    public function testSetStockLocationHeight(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationHeight');
        $this->stockLocation->setStockLocationHeight($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetStockLocationZone(): void
    {
        $expected = 'stockLocationZone';
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationZone');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getStockLocationZone());
    }

    public function testSetStockLocationZone(): void
    {
        $expected = 'stockLocationZone';
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('stockLocationZone');
        $this->stockLocation->setStockLocationZone($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('createdAt');
        $this->stockLocation->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('updatedAt');
        $property->setValue($this->stockLocation, $expected);
        $this->assertSame($expected, $this->stockLocation->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockLocation::class))
            ->getProperty('updatedAt');
        $this->stockLocation->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockLocation));
    }
}
