<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\DeviceCommand;

final class DeviceCommandTest extends TestCase
{
    public function testItCapturesAQueuedRetrievalCommand(): void
    {
        $command = $this->command('retrieve', DeviceCommand::STATUS_QUEUED);

        self::assertSame('retrieve', $command->commandType);
        self::assertSame(DeviceCommand::STATUS_QUEUED, $command->status);
    }

    public function testItRejectsAnUnsupportedCommandType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command('rotate', DeviceCommand::STATUS_QUEUED);
    }

    private function command(string $type, string $status): DeviceCommand
    {
        return new DeviceCommand(
            'id',
            'tenant',
            'device',
            $type,
            'location',
            'manual',
            'reference',
            'request',
            $status,
            null,
            'user',
            new DateTimeImmutable(),
        );
    }
}
