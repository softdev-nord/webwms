<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Unit\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        DateTimeServiceTest
 */
#[CoversClass(DateTimeService::class)]
final class DateTimeServiceTest extends TestCase
{
    public function testCreateDateTimeReturnsCorrectTimezone(): void
    {
        $dateTimeService = new DateTimeService();
        $dateTime = $dateTimeService->createDateTime();

        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertEquals('Europe/Berlin', $dateTime->getTimezone()->getName());
    }
}
