<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\WcsIntegrationService;
use WebWMS\Integration\Domain\MachineCommand;
use WebWMS\Integration\Domain\MachineStatus;
use WebWMS\Integration\Domain\WcsRepository;

final class WcsIntegrationServiceTest extends TestCase
{
    public function testItReturnsAnExistingIdempotentCommand(): void
    {
        $existing = new MachineCommand('id', 'tenant', 'connection', 'transport', 'A', 'B', 'LOAD', 'request', MachineCommand::STATUS_QUEUED, null, 'user', new DateTimeImmutable());
        $repository = $this->createMock(WcsRepository::class);
        $repository->expects(self::once())->method('commandByRequestId')->with('tenant', 'request')->willReturn($existing);
        $repository->expects(self::never())->method('connection');
        $repository->expects(self::never())->method('addCommand');

        $result = (new WcsIntegrationService($repository))->queueCommand('tenant', 'connection', 'transport', 'A', 'B', 'LOAD', 'request', 'user', new DateTimeImmutable());

        self::assertSame($existing, $result);
    }

    public function testItReturnsAnExistingMachineStatusEvent(): void
    {
        $existing = new MachineStatus('id', 'tenant', 'connection', null, 'MACHINE', 'ready', null, 'event', 'user', new DateTimeImmutable());
        $repository = $this->createMock(WcsRepository::class);
        $repository->expects(self::once())->method('statusByExternalEventId')->with('tenant', 'event')->willReturn($existing);
        $repository->expects(self::never())->method('connection');
        $repository->expects(self::never())->method('addMachineStatus');

        $result = (new WcsIntegrationService($repository))->recordStatus('tenant', 'connection', null, 'MACHINE', 'ready', null, 'event', 'user', new DateTimeImmutable());

        self::assertSame($existing, $result);
    }
}
