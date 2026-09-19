<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\DeliverErpStatusEventHandler;
use WebWMS\Integration\Application\PublishedIntegrationMessage;
use WebWMS\Integration\Domain\ErpConnection;
use WebWMS\Integration\Domain\ErpConnectionRepository;
use WebWMS\Integration\Domain\ErpStatusTransport;

final class DeliverErpStatusEventHandlerTest extends TestCase
{
    public function testItDeliversAnEventToEveryActiveTenantConnection(): void
    {
        $first = $this->connection('first');
        $second = $this->connection('second');
        $message = new PublishedIntegrationMessage(
            'message',
            'tenant',
            'fulfillment.shipment.dispatched',
            'shipment',
            'shipment-id',
            ['status' => 'dispatched'],
            '2026-09-19T14:00:00+00:00',
        );
        $connections = $this->createMock(ErpConnectionRepository::class);
        $connections->expects(self::once())->method('activeForTenant')->with('tenant')->willReturn([$first, $second]);
        $transport = $this->createMock(ErpStatusTransport::class);
        $transport->expects(self::exactly(2))->method('deliver')->willReturnCallback(
            static function (ErpConnection $connection, PublishedIntegrationMessage $delivered) use ($message): void {
                self::assertContains($connection->id, ['first', 'second']);
                self::assertSame($message, $delivered);
            },
        );

        (new DeliverErpStatusEventHandler($connections, $transport))($message);
    }

    private function connection(string $id): ErpConnection
    {
        return new ErpConnection(
            $id,
            'tenant',
            $id,
            'https://erp.example.com/' . $id,
            'ERP_SIGNING_KEY',
            true,
            'user',
            new DateTimeImmutable('2026-09-19 14:00:00'),
        );
    }
}
