<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockRotation;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationTest
 *
 * @covers \WebWMS\Entity\StockRotation
 */
final class StockRotationTest extends TestCase
{
    private StockRotation $stockRotation;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockRotation = new StockRotation();
        $this->dateTime = new \DateTimeImmutable();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->stockRotation);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('id');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('id');
        $this->stockRotation->setId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetStockLocationId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('stockLocationId');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getStockLocationId());
    }

    public function testSetStockLocationId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('stockLocationId');
        $this->stockRotation->setStockLocationId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('articleId');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getArticleId());
    }

    public function testSetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('articleId');
        $this->stockRotation->setArticleId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetUsrId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('usrId');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getUsrId());
    }

    public function testSetUsrId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('usrId');
        $this->stockRotation->setUsrId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetCustomerOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('customerOrderId');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getCustomerOrderId());
    }

    public function testSetCustomerOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('customerOrderId');
        $this->stockRotation->setCustomerOrderId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetSupplierOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('supplierOrderId');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getSupplierOrderId());
    }

    public function testSetSupplierOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('supplierOrderId');
        $this->stockRotation->setSupplierOrderId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetMovementId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('movementId');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getMovementId());
    }

    public function testSetMovementId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('movementId');
        $this->stockRotation->setMovementId($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetPosQuantity(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('posQuantity');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getPosQuantity());
    }

    public function testSetPosQuantity(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('posQuantity');
        $this->stockRotation->setPosQuantity($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetAccessDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('accessDate');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getAccessDate());
    }

    public function testSetAccessDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('accessDate');
        $this->stockRotation->setAccessDate($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetDispatchDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('dispatchDate');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getDispatchDate());
    }

    public function testSetDispatchDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('dispatchDate');
        $this->stockRotation->setDispatchDate($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetTrType(): void
    {
        $expected = 'trType';
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('trType');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getTrType());
    }

    public function testSetTrType(): void
    {
        $expected = 'trType';
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('trType');
        $this->stockRotation->setTrType($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('createdAt');
        $property->setValue($this->stockRotation, $expected);
        $this->assertSame($expected, $this->stockRotation->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(StockRotation::class))
            ->getProperty('createdAt');
        $this->stockRotation->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->stockRotation));
    }
}
