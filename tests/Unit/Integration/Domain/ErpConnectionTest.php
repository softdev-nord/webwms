<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\ErpConnection;

final class ErpConnectionTest extends TestCase
{
    public function testItAcceptsAnHttpsEndpointAndCredentialReference(): void
    {
        $connection = $this->connection('https://erp.example.com/webwms', 'ERP_ACME_SIGNING_KEY');

        self::assertSame('https://erp.example.com/webwms', $connection->endpointUrl);
        self::assertTrue($connection->active);
    }

    #[DataProvider('invalidConfiguration')]
    public function testItRejectsUnsafeConfiguration(string $endpoint, string $credential): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->connection($endpoint, $credential);
    }

    /** @return iterable<string, array{string, string}> */
    public static function invalidConfiguration(): iterable
    {
        yield 'plain HTTP' => ['http://erp.example.com', 'ERP_SIGNING_KEY'];
        yield 'invalid URL' => ['not-a-url', 'ERP_SIGNING_KEY'];
        yield 'URL with credentials' => ['https://user:password@erp.example.com', 'ERP_SIGNING_KEY'];
        yield 'invalid credential reference' => ['https://erp.example.com', 'secret-value'];
    }

    private function connection(string $endpoint, string $credential): ErpConnection
    {
        return new ErpConnection(
            'connection',
            'tenant',
            'Acme ERP',
            $endpoint,
            $credential,
            true,
            'user',
            new DateTimeImmutable('2026-09-19 14:00:00'),
        );
    }
}
