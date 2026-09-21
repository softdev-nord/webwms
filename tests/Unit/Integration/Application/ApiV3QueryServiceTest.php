<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\StockMovementCriteria;

final class ApiV3QueryServiceTest extends TestCase
{
    public function testStockSelectionConfigurationAndJournalAreTenantScoped(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::exactly(2))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);
        $queries = new ApiV3QueryService($connection);

        self::assertSame([], $queries->stockSelectionRules('tenant-id'));
        self::assertSame([], $queries->stockSelectionEvents('tenant-id'));
    }

    public function testTraceabilityViewsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::exactly(3))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        $result = (new ApiV3QueryService($connection))->traceability('tenant-id');

        self::assertSame([], $result['serials']);
    }

    public function testTraceabilityEventsUseOnlySupportedDimensions(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAllAssociative')->with(
            self::callback(static function (string $sql): bool {
                self::assertStringContainsString('e.serial_number = :value', $sql);
                self::assertStringContainsString('e.tenant_id = :tenantId', $sql);

                return true;
            }),
            ['tenantId' => 'tenant-id', 'value' => 'SN-001'],
        )->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->traceabilityEvents('tenant-id', 'serial', 'SN-001'));
    }

    public function testWarehouseTopologyIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::exactly(5))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        $result = (new ApiV3QueryService($connection))->warehouseTopology('tenant-id');

        self::assertSame([], $result['bins']);
    }

    public function testWarehouseOverviewIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::exactly(2))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        $result = (new ApiV3QueryService($connection))->warehouseOverview('tenant-id');

        self::assertSame([], $result['warehouses']);
    }

    public function testPlannedInboundWorklistIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'WHERE d.tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->plannedInboundWorklist('tenant-id'));
    }

    public function testUnplannedReceiptsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'WHERE r.tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);
        self::assertSame([], (new ApiV3QueryService($connection))->unplannedReceipts('tenant-id'));
    }

    public function testTransportEndpointsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static fn (string $sql): bool => str_contains($sql, 'WHERE e.tenant_id = :tenantId')),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->transportEndpoints('tenant-id'));
    }

    public function testMachineCommandJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
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

        self::assertSame([], (new ApiV3QueryService($connection))->machineCommands('tenant-id'));
    }

    public function testAutomationCommandJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
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

        self::assertSame([], (new ApiV3QueryService($connection))->deviceCommands('tenant-id'));
    }

    public function testMeasurementJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
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

        self::assertSame([], (new ApiV3QueryService($connection))->measurements('tenant-id'));
    }

    public function testStockQuotesTheReservedCursorAliasForMariaDb(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('AS `cursor`', $sql);

                    return true;
                }),
                self::isType('array'),
            )
            ->willReturn([]);

        $queries = new ApiV3QueryService($connection);

        self::assertSame([], $queries->stock('tenant-id', null, 200, null));
    }

    public function testStockMovementJournalIsTenantScopedAndNewestFirst(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE e.tenant_id = :tenantId', $sql);
                    self::assertStringContainsString('performed_by_name', $sql);
                    self::assertStringContainsString('ORDER BY e.occurred_at DESC, e.id DESC', $sql);

                    return true;
                }),
                self::isType('array'),
            )
            ->willReturn([]);

        $criteria = new StockMovementCriteria(null, null, null, null);

        self::assertSame([], (new ApiV3QueryService($connection))->stockMovements('tenant-id', $criteria, 100, null));
    }

    public function testOutboundOrderListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE o.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->outboundOrders('tenant-id'));
    }

    public function testPickListQueueIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE l.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->pickLists('tenant-id'));
    }

    public function testPackingQueueIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE p.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->packingOrders('tenant-id'));
    }

    public function testShippingQueueIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE s.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->shipments('tenant-id'));
    }

    public function testLoadingManifestListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE m.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->loadingManifests('tenant-id'));
    }

    public function testAvailableLoadingShipmentsAreLabelledAndUnassigned(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('s.tenant_id = :tenantId', $sql);
                    self::assertStringContainsString("s.status = 'labelled'", $sql);
                    self::assertStringContainsString('NOT EXISTS', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->shipmentsAvailableForLoading('tenant-id'));
    }

    public function testOutboxListIsRestrictedByTenantAndStatus(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
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

        self::assertSame([], (new ApiV3QueryService($connection))->outboxMessages(
            'tenant-id',
            'dead_letter',
            50,
            null,
        ));
    }

    public function testOutboxDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['messageId' => 'message-id', 'tenantId' => 'tenant-id'],
            )
            ->willReturn(false);

        self::assertNull((new ApiV3QueryService($connection))->outboxMessage('tenant-id', 'message-id'));
    }

    public function testErpConnectionListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->erpConnections('tenant-id'));
    }

    public function testErpConnectionDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['connectionId' => 'connection-id', 'tenantId' => 'tenant-id'],
            )
            ->willReturn(false);

        self::assertNull((new ApiV3QueryService($connection))->erpConnection('tenant-id', 'connection-id'));
    }

    public function testCarrierConnectionListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->carrierConnections('tenant-id'));
    }

    public function testCarrierConnectionDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['id' => 'connection-id', 'tenantId' => 'tenant-id'],
            )
            ->willReturn(false);

        self::assertNull((new ApiV3QueryService($connection))->carrierConnection('tenant-id', 'connection-id'));
    }

    public function testPrintJobListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
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

        self::assertSame([], (new ApiV3QueryService($connection))->printJobs('tenant-id'));
    }

    public function testPrintJobDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE j.tenant_id = :tenantId AND j.id = :id', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id', 'id' => 'job-id'],
            )
            ->willReturn(false);

        self::assertNull((new ApiV3QueryService($connection))->printJob('tenant-id', 'job-id'));
    }

    public function testPrinterListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(self::isType('string'), ['tenantId' => 'tenant-id'])
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->printers('tenant-id'));
    }

    public function testPrinterDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE tenant_id = :tenantId AND id = :id', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id', 'id' => 'printer-id'],
            )
            ->willReturn(false);

        self::assertNull((new ApiV3QueryService($connection))->printer('tenant-id', 'printer-id'));
    }

    public function testDeviceListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAllAssociative')
            ->with(self::isType('string'), ['tenantId' => 'tenant-id'])
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->devices('tenant-id'));
    }

    public function testScanJournalIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('d.tenant_id = e.tenant_id', $sql);
                    self::assertStringContainsString('WHERE e.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], (new ApiV3QueryService($connection))->scanEvents('tenant-id'));
    }

    public function testScanDetailIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAssociative')
            ->with(
                self::isType('string'),
                ['tenantId' => 'tenant-id', 'id' => 'event-id'],
            )
            ->willReturn(false);

        self::assertNull((new ApiV3QueryService($connection))->scanEvent('tenant-id', 'event-id'));
    }
}
