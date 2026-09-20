<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\MachineCommand;

final class MachineCommandTest extends TestCase
{
    public function testItCapturesAQueuedTransport(): void
    {
        $command = $this->command('transport', MachineCommand::STATUS_QUEUED);

        self::assertSame('LOAD-01', $command->loadUnit);
        self::assertSame(MachineCommand::STATUS_QUEUED, $command->status);
    }

    public function testItRejectsAnUnsupportedStatus(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->command('transport', 'unknown');
    }

    private function command(string $type, string $status): MachineCommand
    {
        return new MachineCommand('id', 'tenant', 'connection', $type, 'SOURCE', 'TARGET', 'LOAD-01', 'request', $status, null, 'user', new DateTimeImmutable());
    }
}
