<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\DeviceIntegrationService;
use WebWMS\Integration\Domain\Device;
use WebWMS\Integration\Domain\DeviceRepository;
use WebWMS\Integration\Domain\ScanEvent;

final class DeviceIntegrationServiceTest extends TestCase
{
    public function testItReturnsAnExistingIdempotentScan(): void
    {
        $existing = new ScanEvent('event', 'tenant', 'device', 'product', 'SKU-1', 'picking', 'PICK-1', 'request', ScanEvent::STATUS_ACCEPTED, null, 'user', new DateTimeImmutable());
        $repository = $this->createMock(DeviceRepository::class);
        $repository->expects(self::once())->method('scanByRequestId')->with('tenant', 'request')->willReturn($existing);
        $repository->expects(self::never())->method('device');
        $repository->expects(self::never())->method('addScan');

        $result = (new DeviceIntegrationService($repository))->recordScan(
            'tenant', 'device', 'product', 'SKU-1', 'picking', 'PICK-1', 'request', true, null, 'user', new DateTimeImmutable(),
        );

        self::assertSame($existing, $result);
    }

    public function testItRequiresAnActiveDeviceBeforePersisting(): void
    {
        $repository = $this->createMock(DeviceRepository::class);
        $repository->expects(self::once())->method('scanByRequestId')->willReturn(null);
        $repository->expects(self::once())->method('device')->with('tenant', 'device', true)->willReturn(
            new Device('device', 'tenant', 'MDE-01', 'MDE 1', 'mde', true, 'user', new DateTimeImmutable()),
        );
        $repository->expects(self::once())->method('addScan')->willReturnArgument(0);

        $event = (new DeviceIntegrationService($repository))->recordScan(
            'tenant', 'device', 'shipment', 'SHIP-1', 'loading', 'LOAD-1', 'request', true, null, 'user', new DateTimeImmutable(),
        );

        self::assertSame(ScanEvent::STATUS_ACCEPTED, $event->status);
    }
}
