<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\Device;

final class DeviceTest extends TestCase
{
    public function testItAcceptsASupportedDevice(): void
    {
        $device = new Device('id', 'tenant', 'MDE-01', 'MDE 1', 'mde', true, 'user', new DateTimeImmutable());

        self::assertTrue($device->active);
        self::assertSame('MDE-01', $device->code);
    }

    #[DataProvider('invalidDeviceProvider')]
    public function testItRejectsInvalidConfiguration(string $code, string $type): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Device('id', 'tenant', $code, 'MDE 1', $type, true, 'user', new DateTimeImmutable());
    }

    /** @return iterable<string, array{string, string}> */
    public static function invalidDeviceProvider(): iterable
    {
        yield 'lowercase code' => ['mde-01', 'mde'];
        yield 'unsupported type' => ['MDE-01', 'forklift'];
    }
}
