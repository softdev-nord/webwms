<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'DateTimeServiceTest'
)]
#[CoversClass(DateTimeService::class)]
final class DateTimeServiceTest extends TestCase
{
    public function testCreateDateTimeReturnsCorrectTimezone(): void
    {
        $dateTimeService = new DateTimeService();
        $dateTime = $dateTimeService->createDateTime();

        self::assertInstanceOf(DateTime::class, $dateTime);
        self::assertSame('Europe/Berlin', $dateTime->getTimezone()->getName());
    }
}
