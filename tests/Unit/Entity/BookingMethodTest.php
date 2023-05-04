<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\BookingMethod;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethodTest
 *
 * @covers \WebWMS\Entity\BookingMethod
 */
final class BookingMethodTest extends TestCase
{
    private BookingMethod $bookingMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookingMethod = new BookingMethod();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bookingMethod);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('id');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('id');
        $this->bookingMethod->setId($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetConfirmation(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('confirmation');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getConfirmation());
    }

    public function testSetConfirmation(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('confirmation');
        $this->bookingMethod->setConfirmation($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetMovementType(): void
    {
        $expected = 'movementType';
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('movementType');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getMovementType());
    }

    public function testSetMovementType(): void
    {
        $expected = 'movementType';
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('movementType');
        $this->bookingMethod->setMovementType($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetDescription(): void
    {
        $expected = 'description';
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('description');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getDescription());
    }

    public function testSetDescription(): void
    {
        $expected = 'description';
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('description');
        $this->bookingMethod->setDescription($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetAnsteuerung(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('ansteuerung');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getAnsteuerung());
    }

    public function testSetAnsteuerung(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('ansteuerung');
        $this->bookingMethod->setAnsteuerung($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetUpload(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('upload');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getUpload());
    }

    public function testSetUpload(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('upload');
        $this->bookingMethod->setUpload($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetStatistics(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('statistics');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getStatistics());
    }

    public function testSetStatistics(): void
    {
        $expected = 42;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('statistics');
        $this->bookingMethod->setStatistics($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetPriority(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('priority');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getPriority());
    }

    public function testSetPriority(): void
    {
        $expected = 42;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('priority');
        $this->bookingMethod->setPriority($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }

    public function testGetTidDescription(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('tidDescription');
        $property->setValue($this->bookingMethod, $expected);
        self::assertSame($expected, $this->bookingMethod->getTidDescription());
    }

    public function testSetTidDescription(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(BookingMethod::class))
            ->getProperty('tidDescription');
        $this->bookingMethod->setTidDescription($expected);
        self::assertSame($expected, $property->getValue($this->bookingMethod));
    }
}
