<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\IntegrationTransportService;
use WebWMS\Integration\Domain\IntegrationTransportRepository;
use WebWMS\Integration\Domain\ProtocolConfiguration;
use WebWMS\Integration\Domain\TransportEndpoint;

class IntegrationTransportServiceTest extends TestCase
{
    public function testItRegistersEndpointAndProtocolAtomically(): void
    {
        $repository = $this->createMock(IntegrationTransportRepository::class);
        $repository->expects($this->once())->method('add')->with(
            self::isInstanceOf(TransportEndpoint::class),
            self::callback(static fn (ProtocolConfiguration $configuration): bool => $configuration->protocol === 'raw_tcp'),
        );

        $endpoint = new IntegrationTransportService($repository)->register(
            'tenant', 'tcp-01', 'Conveyor', 'tcp_client', 'tcp://machine.local:9100', 'tcp_token',
            'raw_tcp', 'stx_etx', 3000, 10000, true, 'user', new DateTimeImmutable(),
        );

        self::assertSame('TCP-01', $endpoint->code);
        self::assertSame('TCP_TOKEN', $endpoint->credentialEnv);
    }
}
