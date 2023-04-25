<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosTest
 *
 * @covers \WebWMS\Entity\CustomerOrderPos
 */
final class CustomerOrderPosTest extends TestCase
{
    private CustomerOrderPos $customerOrderPos;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrderPos = new CustomerOrderPos();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->customerOrderPos);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('id');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('id');
        $this->customerOrderPos->setId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetCustomerOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('customerOrderId');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getCustomerOrderId());
    }

    public function testSetCustomerOrderId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('customerOrderId');
        $this->customerOrderPos->setCustomerOrderId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('articleId');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getArticleId());
    }

    public function testSetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('articleId');
        $this->customerOrderPos->setArticleId($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetArticleNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('articleNr');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getArticleNr());
    }

    public function testSetArticleNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('articleNr');
        $this->customerOrderPos->setArticleNr($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetArticleName(): void
    {
        $expected = 'articleName';
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('articleName');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getArticleName());
    }

    public function testSetArticleName(): void
    {
        $expected = 'articleName';
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('articleName');
        $this->customerOrderPos->setArticleName($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetQuantity(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('quantity');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getQuantity());
    }

    public function testSetQuantity(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('quantity');
        $this->customerOrderPos->setQuantity($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetCustomerOrder(): void
    {
        $expected = $this->createMock(CustomerOrder::class);
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('customerOrder');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getCustomerOrder());
    }

    public function testSetCustomerOrder(): void
    {
        $expected = $this->createMock(CustomerOrder::class);
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('customerOrder');
        $this->customerOrderPos->setCustomerOrder($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('createdAt');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('createdAt');
        $this->customerOrderPos->setCreatedAt($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('updatedAt');
        $property->setValue($this->customerOrderPos, $expected);
        self::assertSame($expected, $this->customerOrderPos->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(CustomerOrderPos::class))
            ->getProperty('updatedAt');
        $this->customerOrderPos->setUpdatedAt($expected);
        self::assertSame($expected, $property->getValue($this->customerOrderPos));
    }
}
