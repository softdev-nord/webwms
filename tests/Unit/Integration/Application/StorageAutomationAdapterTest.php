<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\StorageAutomationAdapter;
use WebWMS\Integration\Domain\AutomationDevice;
use WebWMS\Integration\Domain\AutomationRepository;
use WebWMS\Integration\Domain\DeviceCommand;

final class StorageAutomationAdapterTest extends TestCase
{
    public function testItReturnsAnExistingIdempotentCommand(): void
    {
        $existing = $this->command();
        $repository = $this->createMock(AutomationRepository::class);
        $repository->expects(self::once())->method('commandByRequestId')->with('tenant', 'request')->willReturn($existing);
        $repository->expects(self::never())->method('device');
        $repository->expects(self::never())->method('addCommand');

        $result = (new StorageAutomationAdapter($repository))->queueCommand(
            'tenant',
            'device',
            'present',
            'location',
            'manual',
            'reference',
            'request',
            'user',
            new DateTimeImmutable(),
        );

        self::assertSame($existing, $result);
    }

    public function testItRequiresAnActiveDeviceBeforeQueueing(): void
    {
        $repository = $this->createMock(AutomationRepository::class);
        $repository->expects(self::once())->method('commandByRequestId')->willReturn(null);
        $repository->expects(self::once())->method('device')->with('tenant', 'device', true)->willReturn(
            new AutomationDevice(
                'device',
                'tenant',
                'LIFT-01',
                'Lift',
                'storage_lift',
                'https://lift.example/commands',
                'LIFT_TOKEN',
                true,
                'user',
                new DateTimeImmutable(),
            ),
        );
        $repository->expects(self::once())->method('addCommand')->willReturnArgument(0);

        $command = (new StorageAutomationAdapter($repository))->queueCommand(
            'tenant',
            'device',
            'present',
            'location',
            'manual',
            'reference',
            'request',
            'user',
            new DateTimeImmutable(),
        );

        self::assertSame(DeviceCommand::STATUS_QUEUED, $command->status);
    }

    private function command(): DeviceCommand
    {
        return new DeviceCommand(
            'id',
            'tenant',
            'device',
            'present',
            'location',
            'manual',
            'reference',
            'request',
            DeviceCommand::STATUS_QUEUED,
            null,
            'user',
            new DateTimeImmutable(),
        );
    }
}
