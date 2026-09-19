<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Infrastructure\Transport;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use WebWMS\Integration\Application\PublishedIntegrationMessage;
use WebWMS\Integration\Domain\CredentialProvider;
use WebWMS\Integration\Domain\ErpConnection;
use WebWMS\Integration\Infrastructure\Transport\HttpErpStatusTransport;

final class HttpErpStatusTransportTest extends TestCase
{
    public function testItPostsASignedIdempotentStatusEvent(): void
    {
        $response = new MockResponse('', ['http_code' => 202]);
        $client = new MockHttpClient($response);
        $credentials = $this->createMock(CredentialProvider::class);
        $credentials->expects(self::once())->method('secret')->with('ERP_SIGNING_KEY')->willReturn('test-secret');

        (new HttpErpStatusTransport($client, $credentials))->deliver($this->connection(), $this->message());

        self::assertSame('POST', $response->getRequestMethod());
        self::assertSame('https://erp.example.com/webwms/status-events', $response->getRequestUrl());
        $headers = $response->getRequestOptions()['headers'] ?? [];
        self::assertContains('Idempotency-Key: message-id', $headers);
        self::assertContains('X-WebWMS-Event: fulfillment.shipment.dispatched', $headers);
        self::assertNotEmpty(array_filter(
            $headers,
            static fn (string $header): bool => str_starts_with($header, 'X-WebWMS-Signature: sha256='),
        ));
    }

    public function testItRejectsANonSuccessfulResponse(): void
    {
        $client = new MockHttpClient(new MockResponse('', ['http_code' => 503]));
        $credentials = $this->createStub(CredentialProvider::class);
        $credentials->method('secret')->willReturn('test-secret');

        $this->expectException(\RuntimeException::class);
        (new HttpErpStatusTransport($client, $credentials))->deliver($this->connection(), $this->message());
    }

    private function connection(): ErpConnection
    {
        return new ErpConnection(
            'connection',
            'tenant',
            'ERP',
            'https://erp.example.com/webwms',
            'ERP_SIGNING_KEY',
            true,
            'user',
            new DateTimeImmutable('2026-09-19 14:00:00'),
        );
    }

    private function message(): PublishedIntegrationMessage
    {
        return new PublishedIntegrationMessage(
            'message-id',
            'tenant',
            'fulfillment.shipment.dispatched',
            'shipment',
            'shipment-id',
            ['status' => 'dispatched'],
            '2026-09-19T14:00:00+00:00',
        );
    }
}
