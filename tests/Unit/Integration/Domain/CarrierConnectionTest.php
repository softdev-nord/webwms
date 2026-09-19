<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\CarrierConnection;

final class CarrierConnectionTest extends TestCase
{
    public function testItAcceptsSafeCarrierConfiguration(): void
    {
        $connection = $this->connection('DHL', 'https://carrier.example.com/api', 'DHL_API_TOKEN');

        self::assertSame('DHL', $connection->carrierCode);
        self::assertTrue($connection->active);
    }

    #[DataProvider('invalidConfiguration')]
    public function testItRejectsUnsafeConfiguration(string $code, string $endpoint, string $credential): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->connection($code, $endpoint, $credential);
    }

    /** @return iterable<string, array{string, string, string}> */
    public static function invalidConfiguration(): iterable
    {
        yield 'lowercase code' => ['dhl', 'https://carrier.example.com', 'DHL_TOKEN'];
        yield 'plain HTTP' => ['DHL', 'http://carrier.example.com', 'DHL_TOKEN'];
        yield 'credentials in URL' => ['DHL', 'https://user:pass@carrier.example.com', 'DHL_TOKEN'];
        yield 'literal secret' => ['DHL', 'https://carrier.example.com', 'secret'];
    }

    private function connection(string $code, string $endpoint, string $credential): CarrierConnection
    {
        return new CarrierConnection('connection', 'tenant', 'DHL production', $code, $endpoint, $credential, true, 'user', new DateTimeImmutable('2026-09-19 16:00:00'));
    }
}
