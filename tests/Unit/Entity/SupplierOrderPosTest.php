<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Entity\SupplierOrderPos;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosTest
 *
 * @covers \WebWMS\Entity\SupplierOrderPos
 */
final class SupplierOrderPosTest extends TestCase
{
    private SupplierOrderPos $supplierOrderPos;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierOrderPos = new SupplierOrderPos();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->supplierOrderPos);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('id');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('id');
        $this->supplierOrderPos->setId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetSupplierOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('supplierOrderId');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getSupplierOrderId());
    }

    public function testSetSupplierOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('supplierOrderId');
        $this->supplierOrderPos->setSupplierOrderId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('articleId');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getArticleId());
    }

    public function testSetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('articleId');
        $this->supplierOrderPos->setArticleId($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetArticleNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('articleNr');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getArticleNr());
    }

    public function testSetArticleNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('articleNr');
        $this->supplierOrderPos->setArticleNr($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetArticleName(): void
    {
        $expected = 'articleName';
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('articleName');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getArticleName());
    }

    public function testSetArticleName(): void
    {
        $expected = 'articleName';
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('articleName');
        $this->supplierOrderPos->setArticleName($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetQuantity(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('supplierOrderPosQuantity');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getSupplierOrderPosQuantity());
    }

    public function testSetQuantity(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('supplierOrderPosQuantity');
        $this->supplierOrderPos->setSupplierOrderPosQuantity($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetSupplierOrder(): void
    {
        $expected = $this->createMock(SupplierOrder::class);
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('supplierOrder');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getSupplierOrder());
    }

    public function testSetSupplierOrder(): void
    {
        $expected = $this->createMock(SupplierOrder::class);
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('supplierOrder');
        $this->supplierOrderPos->setSupplierOrder($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('createdAt');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('createdAt');
        $this->supplierOrderPos->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('updatedAt');
        $property->setValue($this->supplierOrderPos, $expected);
        $this->assertSame($expected, $this->supplierOrderPos->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(SupplierOrderPos::class))
            ->getProperty('updatedAt');
        $this->supplierOrderPos->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->supplierOrderPos));
    }
}
