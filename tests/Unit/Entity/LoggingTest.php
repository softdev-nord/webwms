<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Logging;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'LoggingTest'
)]
#[CoversClass(Logging::class)]
final class LoggingTest extends TestCase
{
    private Logging $logging;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logging = new Logging();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->logging->setId($id);
        self::assertSame($id, $this->logging->getId());

        // Test setRoute() and getRoute()
        $route = 'route';
        $this->logging->setRoute($route);
        self::assertSame($route, $this->logging->getRoute());

        // Test setMessage() and getMessage()
        $message = 'Test Message';
        $this->logging->setMessage($message);
        self::assertSame($message, $this->logging->getMessage());

        // Test setDate() and getDate()
        $date = $this->dateTime;
        $this->logging->setDate($date);
        self::assertEquals($date, $this->logging->getDate());

        // Test setUser() and getUser()
        $articleName = 'Rene Irrgang';
        $this->logging->setUser($articleName);
        self::assertSame($articleName, $this->logging->getUser());

        // Test setIpAddress() and getIpAddress()
        $quantity = '123.123.123.123';
        $this->logging->setIpAddress($quantity);
        self::assertSame($quantity, $this->logging->getIpAddress());

        // Test setUserAgent() and getUserAgent()
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        $this->logging->setUserAgent($userAgent);
        self::assertSame($userAgent, $this->logging->getUserAgent());
    }
}
