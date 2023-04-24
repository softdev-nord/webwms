<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockZone;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneTest
 *
 * @covers \WebWMS\Entity\StockZone
 */
final class StockZoneTest extends TestCase
{
    private StockZone $stockZone;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockZone = new StockZone();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockZone);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('id');
        $property->setValue($this->stockZone, $expected);
        $this->assertSame($expected, $this->stockZone->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('id');
        $this->stockZone->setId($expected);
        $this->assertSame($expected, $property->getValue($this->stockZone));
    }

    public function testGetStockZoneShortDesc(): void
    {
        $expected = 'stockZoneShortDesc';
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('stockZoneShortDesc');
        $property->setValue($this->stockZone, $expected);
        $this->assertSame($expected, $this->stockZone->getStockZoneShortDesc());
    }

    public function testSetStockZoneShortDesc(): void
    {
        $expected = 'stockZoneShortDesc';
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('stockZoneShortDesc');
        $this->stockZone->setStockZoneShortDesc($expected);
        $this->assertSame($expected, $property->getValue($this->stockZone));
    }

    public function testGetStockZoneDescription(): void
    {
        $expected = 'stockZoneDescription';
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('stockZoneDescription');
        $property->setValue($this->stockZone, $expected);
        $this->assertSame($expected, $this->stockZone->getStockZoneDescription());
    }

    public function testSetStockZoneDescription(): void
    {
        $expected = 'stockZoneDescription';
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('stockZoneDescription');
        $this->stockZone->setStockZoneDescription($expected);
        $this->assertSame($expected, $property->getValue($this->stockZone));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockZone, $expected);
        $this->assertSame($expected, $this->stockZone->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('createdAt');
        $this->stockZone->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockZone));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('updatedAt');
        $property->setValue($this->stockZone, $expected);
        $this->assertSame($expected, $this->stockZone->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockZone::class))
            ->getProperty('updatedAt');
        $this->stockZone->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockZone));
    }
}
