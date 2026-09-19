<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Infrastructure\Transport;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use WebWMS\Integration\Domain\CredentialProvider;
use WebWMS\Integration\Domain\Printer;
use WebWMS\Integration\Domain\PrintJob;
use WebWMS\Integration\Infrastructure\Transport\HttpPrintTransport;

final class HttpPrintTransportTest extends TestCase
{
    public function testItSubmitsAnIdempotentPrintJob(): void
    {
        $response = new MockResponse('{"jobReference":"remote-42"}', ['http_code' => 201]);
        $credentials = $this->createStub(CredentialProvider::class);
        $credentials->method('secret')->willReturn('token');
        $transport = new HttpPrintTransport(new MockHttpClient($response), $credentials);

        self::assertSame('remote-42', $transport->print($this->printer(), $this->job()));
        self::assertSame('https://printer.example.com/api/print-jobs', $response->getRequestUrl());
        self::assertContains('Idempotency-Key: request-1', $response->getRequestOptions()['headers'] ?? []);
        self::assertContains('Authorization: Bearer token', $response->getRequestOptions()['headers'] ?? []);
    }

    public function testItRejectsAnIncompleteResponse(): void
    {
        $credentials = $this->createStub(CredentialProvider::class);
        $credentials->method('secret')->willReturn('token');

        $this->expectException(\RuntimeException::class);
        (new HttpPrintTransport(new MockHttpClient(new MockResponse('{}')), $credentials))->print($this->printer(), $this->job());
    }

    private function printer(): Printer
    {
        return new Printer('printer', 'tenant', 'Zebra dock 1', 'https://printer.example.com/api', 'PRINTER_TOKEN', true, 'user', new DateTimeImmutable());
    }

    private function job(): PrintJob
    {
        return new PrintJob('job', 'tenant', 'printer', 'carrier_label', 'label://1', 'ZPL', 1, 'request-1', PrintJob::STATUS_PRINTING, 1, null, null, 'user', new DateTimeImmutable());
    }
}
