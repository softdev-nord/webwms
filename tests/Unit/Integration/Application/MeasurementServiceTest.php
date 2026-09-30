<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\MeasurementService;
use WebWMS\Integration\Domain\Measurement;
use WebWMS\Integration\Domain\MeasurementDevice;
use WebWMS\Integration\Domain\MeasurementRepository;

class MeasurementServiceTest extends TestCase
{
    public function testItReturnsAnExistingIdempotentMeasurement(): void
    {
        $existing = $this->measurement();
        $repository = $this->createMock(MeasurementRepository::class);
        $repository->expects($this->once())->method('measurementByRequestId')->with('tenant', 'request')->willReturn($existing);
        $repository->expects($this->never())->method('device');
        $repository->expects($this->never())->method('addMeasurement');

        self::assertSame($existing, $this->service($repository)->record(
            'tenant', 'device', 'package', 'target', 1000, null, null, null,
            'request', true, null, 'user', new DateTimeImmutable(),
        ));
    }

    public function testItValidatesTheActiveDeviceCapability(): void
    {
        $repository = $this->createMock(MeasurementRepository::class);
        $repository->expects($this->once())->method('measurementByRequestId')->willReturn(null);
        $repository->expects($this->once())->method('device')->with('tenant', 'device', true)->willReturn(
            new MeasurementDevice('device', 'tenant', 'SCALE-01', 'Waage', 'scale', true, 'user', new DateTimeImmutable()),
        );
        $repository->expects($this->once())->method('addMeasurement')->willReturnArgument(0);

        $measurement = $this->service($repository)->record(
            'tenant', 'device', 'package', 'target', 1000, null, null, null,
            'request', true, null, 'user', new DateTimeImmutable(),
        );

        self::assertSame(1000, $measurement->weightGrams);
    }

    private function service(MeasurementRepository $repository): MeasurementService
    {
        return new MeasurementService($repository);
    }

    private function measurement(): Measurement
    {
        return new Measurement(
            'id', 'tenant', 'device', 'package', 'target', 1000, null, null, null,
            'request', 'accepted', null, 'user', new DateTimeImmutable(),
        );
    }
}
