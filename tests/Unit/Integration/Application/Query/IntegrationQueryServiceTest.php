<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application\Query;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\Query\IntegrationQueryService;

class IntegrationQueryServiceTest extends TestCase
{
    public function testDataExchangeViewsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(4))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')
                || str_contains($sql, 'tenant_id = o.tenant_id')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);
        $queries = new IntegrationQueryService($connection);

        self::assertSame([], $queries->exchangeJobs('tenant-id'));
        self::assertSame([], $queries->integrationMappings('tenant-id'));
        self::assertSame([], $queries->commerceConnections('tenant-id'));
        self::assertSame([], $queries->channelOrders('tenant-id'));
    }

    public function testTransportEndpointsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static fn (string $sql): bool => str_contains($sql, 'WHERE e.tenant_id = :tenantId')),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->transportEndpoints('tenant-id'));
    }

    public function testMachineCommandJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('w.tenant_id = c.tenant_id', $sql);
                    self::assertStringContainsString('WHERE c.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->machineCommands('tenant-id'));
    }

    public function testAutomationCommandJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('d.tenant_id = c.tenant_id', $sql);
                    self::assertStringContainsString('WHERE c.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->deviceCommands('tenant-id'));
    }

    public function testMeasurementJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('d.tenant_id = m.tenant_id', $sql);
                    self::assertStringContainsString('WHERE m.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->measurements('tenant-id'));
    }

    public function testOutboxListIsRestrictedByTenantAndStatus(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('tenant_id = :tenantId AND status = :status', $sql);

                    return true;
                }),
                [
                    'tenantId' => 'tenant-id',
                    'status' => 'dead_letter',
                    'cursorFilter' => null,
                    'cursorValue' => '',
                ],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->outboxMessages(
            'tenant-id',
            'dead_letter',
            50,
            null,
        ));
    }

    public function testOutboxDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['messageId' => 'message-id', 'tenantId' => 'tenant-id'],
            )
            ->willReturn(false);

        self::assertNull(new IntegrationQueryService($connection)->outboxMessage('tenant-id', 'message-id'));
    }

    public function testErpConnectionListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->erpConnections('tenant-id'));
    }

    public function testErpConnectionDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['connectionId' => 'connection-id', 'tenantId' => 'tenant-id'],
            )
            ->willReturn(false);

        self::assertNull(new IntegrationQueryService($connection)->erpConnection('tenant-id', 'connection-id'));
    }

    public function testCarrierConnectionListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->carrierConnections('tenant-id'));
    }

    public function testCarrierConnectionDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['id' => 'connection-id', 'tenantId' => 'tenant-id'],
            )
            ->willReturn(false);

        self::assertNull(new IntegrationQueryService($connection)->carrierConnection('tenant-id', 'connection-id'));
    }

    public function testPrintJobListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('p.tenant_id = j.tenant_id', $sql);
                    self::assertStringContainsString('WHERE j.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->printJobs('tenant-id'));
    }

    public function testPrintJobDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE j.tenant_id = :tenantId AND j.id = :id', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id', 'id' => 'job-id'],
            )
            ->willReturn(false);

        self::assertNull(new IntegrationQueryService($connection)->printJob('tenant-id', 'job-id'));
    }

    public function testPrinterListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(self::isType('string'), ['tenantId' => 'tenant-id'])
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->printers('tenant-id'));
    }

    public function testPrinterDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE tenant_id = :tenantId AND id = :id', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id', 'id' => 'printer-id'],
            )
            ->willReturn(false);

        self::assertNull(new IntegrationQueryService($connection)->printer('tenant-id', 'printer-id'));
    }

    public function testDeviceListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')
            ->with(self::isType('string'), ['tenantId' => 'tenant-id'])
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->devices('tenant-id'));
    }

    public function testScanJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('d.tenant_id = e.tenant_id', $sql);
                    self::assertStringContainsString('WHERE e.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new IntegrationQueryService($connection)->scanEvents('tenant-id'));
    }

    public function testScanDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['tenantId' => 'tenant-id', 'id' => 'event-id'],
            )
            ->willReturn(false);

        self::assertNull(new IntegrationQueryService($connection)->scanEvent('tenant-id', 'event-id'));
    }
}
