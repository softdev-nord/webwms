<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\IntegrationTransportService;
use WebWMS\Integration\Application\RuntimeIntegrationTransport;
use WebWMS\Integration\Domain\ConfiguredTransportEndpoint;
use WebWMS\Integration\Domain\IntegrationTransportRepository;
use WebWMS\Integration\Domain\ProtocolConfiguration;
use WebWMS\Integration\Domain\TransportEndpoint;

class IntegrationTransportServiceTest extends TestCase
{
    public function testItRegistersEndpointAndProtocolAtomically(): void
    {
        $repository = $this->createMock(IntegrationTransportRepository::class);
        $runtimeTransport = $this->createMock(RuntimeIntegrationTransport::class);
        $repository->expects($this->once())->method('add')->with(
            self::isInstanceOf(TransportEndpoint::class),
            self::callback(static fn (ProtocolConfiguration $configuration): bool => $configuration->protocol === 'raw_tcp'),
        );

        $endpoint = new IntegrationTransportService($repository, $runtimeTransport)->register(
            'tenant', 'tcp-01', 'Conveyor', 'tcp_client', 'tcp://machine.local:9100', 'tcp_token',
            'raw_tcp', 'stx_etx', 3000, 10000, true, 'user', new DateTimeImmutable(),
        );

        self::assertSame('TCP-01', $endpoint->code);
        self::assertSame('TCP_TOKEN', $endpoint->credentialEnv);
    }

    public function testItDeliversThroughTheTenantScopedConfiguredEndpoint(): void
    {
        $repository = $this->createMock(IntegrationTransportRepository::class);
        $runtimeTransport = $this->createMock(RuntimeIntegrationTransport::class);
        $target = new ConfiguredTransportEndpoint(
            new TransportEndpoint('id', 'tenant', 'HTTP-01', 'Service', 'http_webservice', 'https://service.example/messages', 'HTTP_TOKEN', true, 'user', new DateTimeImmutable()),
            new ProtocolConfiguration('id', 'rest_json', 'http', 3000, 10000),
        );
        $repository->expects($this->once())->method('configuredEndpoint')->with('tenant', 'id')->willReturn($target);
        $runtimeTransport->expects($this->once())->method('deliver')->with($target, 'message', ['value' => 42]);

        new IntegrationTransportService($repository, $runtimeTransport)->deliver('tenant', 'id', 'message', ['value' => 42]);
    }
}
