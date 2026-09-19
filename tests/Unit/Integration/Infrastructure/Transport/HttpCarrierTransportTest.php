<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Infrastructure\Transport;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use WebWMS\Integration\Domain\CarrierConnection;
use WebWMS\Integration\Domain\CredentialProvider;
use WebWMS\Integration\Infrastructure\Transport\HttpCarrierTransport;

final class HttpCarrierTransportTest extends TestCase
{
    public function testItCreatesAnIdempotentLabelRequest(): void
    {
        $response = new MockResponse('{"trackingNumber":"TRACK-1","labelReference":"label://1"}', ['http_code' => 201]);
        $credentials = $this->createStub(CredentialProvider::class);
        $credentials->method('secret')->willReturn('token');

        $result = (new HttpCarrierTransport(new MockHttpClient($response), $credentials))->createLabel($this->connection(), ['id' => 'shipment'], 'request-1');

        self::assertSame(['trackingNumber' => 'TRACK-1', 'labelReference' => 'label://1'], $result);
        self::assertSame('https://carrier.example.com/api/labels', $response->getRequestUrl());
        self::assertContains('Idempotency-Key: request-1', $response->getRequestOptions()['headers'] ?? []);
        self::assertContains('Authorization: Bearer token', $response->getRequestOptions()['headers'] ?? []);
    }

    public function testItRejectsAnIncompleteResponse(): void
    {
        $credentials = $this->createStub(CredentialProvider::class);
        $credentials->method('secret')->willReturn('token');
        $transport = new HttpCarrierTransport(new MockHttpClient(new MockResponse('{}')), $credentials);

        $this->expectException(\RuntimeException::class);
        $transport->createLabel($this->connection(), ['id' => 'shipment'], 'request-1');
    }

    private function connection(): CarrierConnection
    {
        return new CarrierConnection('connection', 'tenant', 'DHL', 'DHL', 'https://carrier.example.com/api', 'DHL_TOKEN', true, 'user', new DateTimeImmutable());
    }
}
