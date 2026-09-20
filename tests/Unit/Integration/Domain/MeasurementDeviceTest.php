<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\MeasurementDevice;

final class MeasurementDeviceTest extends TestCase
{
    public function testItAcceptsACombinedDevice(): void
    {
        $device = new MeasurementDevice('id', 'tenant', 'MEASURE-01', 'Packplatz', 'combined', true, 'user', new DateTimeImmutable());

        self::assertSame('combined', $device->type);
        self::assertTrue($device->active);
    }

    #[DataProvider('invalidDeviceProvider')]
    public function testItRejectsInvalidConfiguration(string $code, string $type): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MeasurementDevice('id', 'tenant', $code, 'Packplatz', $type, true, 'user', new DateTimeImmutable());
    }

    /** @return iterable<string, array{string, string}> */
    public static function invalidDeviceProvider(): iterable
    {
        yield 'lowercase code' => ['scale-01', 'scale'];
        yield 'unsupported type' => ['SCALE-01', 'scanner'];
    }
}
