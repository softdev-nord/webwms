<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockTransferStrategy;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockTransferStrategyTest
 *
 * @covers \WebWMS\Entity\StockTransferStrategy
 */
final class StockTransferStrategyTest extends TestCase
{
    private StockTransferStrategy $stockTransferStrategy;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockTransferStrategy = new StockTransferStrategy();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockTransferStrategy);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('id');
        $property->setValue($this->stockTransferStrategy, $expected);
        $this->assertSame($expected, $this->stockTransferStrategy->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('id');
        $this->stockTransferStrategy->setId($expected);
        $this->assertSame($expected, $property->getValue($this->stockTransferStrategy));
    }

    public function testGetShortCode(): void
    {
        $expected = 'shortCode';
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('shortCode');
        $property->setValue($this->stockTransferStrategy, $expected);
        $this->assertSame($expected, $this->stockTransferStrategy->getShortCode());
    }

    public function testSetShortCode(): void
    {
        $expected = 'shortCode';
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('shortCode');
        $this->stockTransferStrategy->setShortCode($expected);
        $this->assertSame($expected, $property->getValue($this->stockTransferStrategy));
    }

    public function testGetDescription(): void
    {
        $expected = 'description';
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('description');
        $property->setValue($this->stockTransferStrategy, $expected);
        $this->assertSame($expected, $this->stockTransferStrategy->getDescription());
    }

    public function testSetDescription(): void
    {
        $expected = 'description';
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('description');
        $this->stockTransferStrategy->setDescription($expected);
        $this->assertSame($expected, $property->getValue($this->stockTransferStrategy));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockTransferStrategy, $expected);
        $this->assertSame($expected, $this->stockTransferStrategy->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('createdAt');
        $this->stockTransferStrategy->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockTransferStrategy));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('updatedAt');
        $property->setValue($this->stockTransferStrategy, $expected);
        $this->assertSame($expected, $this->stockTransferStrategy->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockTransferStrategy::class))
            ->getProperty('updatedAt');
        $this->stockTransferStrategy->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockTransferStrategy));
    }
}
