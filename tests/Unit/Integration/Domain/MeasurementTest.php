<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\Measurement;

final class MeasurementTest extends TestCase
{
    public function testItCapturesCombinedPackageValues(): void
    {
        $measurement = $this->measurement(1250, 400, 300, 200);

        self::assertSame(1250, $measurement->weightGrams);
        self::assertSame('package', $measurement->targetType);
    }

    public function testItRequiresCompleteDimensions(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->measurement(null, 400, null, 200);
    }

    private function measurement(?int $weight, ?int $length, ?int $width, ?int $height): Measurement
    {
        return new Measurement(
            'id', 'tenant', 'device', 'package', 'target', $weight, $length, $width, $height,
            'request', 'accepted', null, 'user', new DateTimeImmutable(),
        );
    }
}
