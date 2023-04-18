<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Logging;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LoggingTest
 *
 * @covers \WebWMS\Entity\Logging
 */
final class LoggingTest extends TestCase
{
    private Logging $logging;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logging = new Logging();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->logging);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('id');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('id');
        $this->logging->setId($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }

    public function testGetRoute(): void
    {
        $expected = 'route';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('route');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getRoute());
    }

    public function testSetRoute(): void
    {
        $expected = 'route';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('route');
        $this->logging->setRoute($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }

    public function testGetMessage(): void
    {
        $expected = 'message';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('message');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getMessage());
    }

    public function testSetMessage(): void
    {
        $expected = 'message';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('message');
        $this->logging->setMessage($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }

    public function testGetDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('date');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getDate());
    }

    public function testSetDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('date');
        $this->logging->setDate($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }

    public function testGetUser(): void
    {
        $expected = 'user';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('user');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getUser());
    }

    public function testSetUser(): void
    {
        $expected = 'user';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('user');
        $this->logging->setUser($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }

    public function testGetIpAddress(): void
    {
        $expected = 'ipAddress';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('ipAddress');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getIpAddress());
    }

    public function testSetIpAddress(): void
    {
        $expected = 'ipAddress';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('ipAddress');
        $this->logging->setIpAddress($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }

    public function testGetUserAgent(): void
    {
        $expected = 'userAgent';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('userAgent');
        $property->setValue($this->logging, $expected);
        $this->assertSame($expected, $this->logging->getUserAgent());
    }

    public function testSetUserAgent(): void
    {
        $expected = 'userAgent';
        $property = (new \ReflectionClass(Logging::class))
            ->getProperty('userAgent');
        $this->logging->setUserAgent($expected);
        $this->assertSame($expected, $property->getValue($this->logging));
    }
}
