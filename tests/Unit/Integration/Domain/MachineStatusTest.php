<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\MachineStatus;

final class MachineStatusTest extends TestCase
{
    public function testItAcceptsAFaultStatus(): void
    {
        $status = new MachineStatus('id', 'tenant', 'connection', null, 'CONVEYOR-01', 'fault', 'Emergency stop', 'event', 'user', new DateTimeImmutable());

        self::assertSame('fault', $status->status);
    }

    public function testItRejectsAnUnsupportedStatus(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MachineStatus('id', 'tenant', 'connection', null, 'CONVEYOR-01', 'unknown', null, 'event', 'user', new DateTimeImmutable());
    }
}
