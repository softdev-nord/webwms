<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\TransportEndpoint;

final class TransportEndpointTest extends TestCase
{
    public function testItAcceptsATcpEndpoint(): void
    {
        $endpoint = new TransportEndpoint('id', 'tenant', 'TCP-01', 'Conveyor', 'tcp_client', 'tcp://machine.local:9100', 'TCP_TOKEN', true, 'user', new DateTimeImmutable());

        self::assertSame('tcp_client', $endpoint->adapterType);
    }

    public function testItRejectsAnInsecureWebservice(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TransportEndpoint('id', 'tenant', 'HTTP-01', 'Service', 'http_webservice', 'http://machine.local/api', 'HTTP_TOKEN', true, 'user', new DateTimeImmutable());
    }
}
