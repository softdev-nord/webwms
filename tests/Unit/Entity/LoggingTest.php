<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\LoggingEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LoggingTest
 */
#[CoversClass(LoggingEntity::class)]
final class LoggingTest extends TestCase
{
    private LoggingEntity $loggingEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->loggingEntity = new LoggingEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->loggingEntity->setId($id);
        self::assertEquals($id, $this->loggingEntity->getId());

        // Test setRoute() and getRoute()
        $route = 'route';
        $this->loggingEntity->setRoute($route);
        self::assertEquals($route, $this->loggingEntity->getRoute());

        // Test setMessage() and getMessage()
        $message = 'Test Message';
        $this->loggingEntity->setMessage($message);
        self::assertEquals($message, $this->loggingEntity->getMessage());

        // Test setDate() and getDate()
        $date = $this->dateTime;
        $this->loggingEntity->setDate($date);
        self::assertEquals($date, $this->loggingEntity->getDate());

        // Test setUser() and getUser()
        $articleName = 'Rene Irrgang';
        $this->loggingEntity->setUser($articleName);
        self::assertEquals($articleName, $this->loggingEntity->getUser());

        // Test setIpAddress() and getIpAddress()
        $quantity = '123.123.123.123';
        $this->loggingEntity->setIpAddress($quantity);
        self::assertEquals($quantity, $this->loggingEntity->getIpAddress());

        // Test setUserAgent() and getUserAgent()
        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36';
        $this->loggingEntity->setUserAgent($userAgent);
        self::assertEquals($userAgent, $this->loggingEntity->getUserAgent());
    }
}
