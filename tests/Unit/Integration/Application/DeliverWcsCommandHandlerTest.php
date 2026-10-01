<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\DeliverWcsCommandHandler;
use WebWMS\Integration\Application\PublishedIntegrationMessage;
use WebWMS\Integration\Application\WcsCommandTransport;
use WebWMS\Integration\Domain\MachineCommand;
use WebWMS\Integration\Domain\WcsConnection;
use WebWMS\Integration\Domain\WcsRepository;

class DeliverWcsCommandHandlerTest extends TestCase
{
    public function testItDeliversQueuedCommandAndMarksItDispatched(): void
    {
        $repository = $this->createMock(WcsRepository::class);
        $transport = $this->createMock(WcsCommandTransport::class);
        $command = new MachineCommand('command', 'tenant', 'connection', 'transport', 'A', 'B', 'LOAD', 'request', MachineCommand::STATUS_QUEUED, null, 'user', new DateTimeImmutable());
        $connection = new WcsConnection('connection', 'tenant', 'WCS-01', 'WCS', 'wcs', 'https://wcs.example', 'WCS_TOKEN', true, 'user', new DateTimeImmutable());
        $message = new PublishedIntegrationMessage('message', 'tenant', 'integration.wcs.command.queued', 'machine_command', 'command', [], '2026-10-01T00:00:00+00:00');
        $repository->method('command')->with('tenant', 'command')->willReturn($command);
        $repository->method('connection')->with('tenant', 'connection', true)->willReturn($connection);
        $transport->expects($this->once())->method('deliver')->with($connection, $message);
        $repository->expects($this->once())->method('transitionCommand')->with(
            'tenant', 'command', MachineCommand::STATUS_DISPATCHED, null, 'user', self::isInstanceOf(DateTimeImmutable::class),
        );

        new DeliverWcsCommandHandler($repository, $transport)($message);
    }

    public function testItIgnoresOtherIntegrationEvents(): void
    {
        $repository = $this->createMock(WcsRepository::class);
        $transport = $this->createMock(WcsCommandTransport::class);
        $repository->expects($this->never())->method('command');

        new DeliverWcsCommandHandler($repository, $transport)(
            new PublishedIntegrationMessage('message', 'tenant', 'other.event', 'order', 'order', [], '2026-10-01T00:00:00+00:00'),
        );
    }
}
