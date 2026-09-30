<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Warehouse\Application\Query;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\StockMovementCriteria;
use WebWMS\Warehouse\Application\Query\WarehouseQueryService;

class WarehouseQueryServiceTest extends TestCase
{
    public function testStockBlockViewsAreTenantScoped(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(3))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            self::isType('array'),
        )->willReturn([]);
        $queries = new WarehouseQueryService($connection);

        self::assertSame([], $queries->stockBlockReasons('tenant-id'));
        self::assertSame([], $queries->stockBlocks('tenant-id'));
        self::assertSame([], $queries->stockBlockEvents('tenant-id', 'block-id'));
    }

    public function testStockSelectionConfigurationAndJournalAreTenantScoped(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(2))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);
        $queries = new WarehouseQueryService($connection);

        self::assertSame([], $queries->stockSelectionRules('tenant-id'));
        self::assertSame([], $queries->stockSelectionEvents('tenant-id'));
    }

    public function testTraceabilityViewsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(3))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        $result = new WarehouseQueryService($connection)->traceability('tenant-id');

        self::assertSame([], $result['serials']);
    }

    public function testTraceabilityEventsUseOnlySupportedDimensions(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')->with(
            self::callback(static function (string $sql): bool {
                self::assertStringContainsString('e.serial_number = :value', $sql);
                self::assertStringContainsString('e.tenant_id = :tenantId', $sql);

                return true;
            }),
            ['tenantId' => 'tenant-id', 'value' => 'SN-001'],
        )->willReturn([]);

        self::assertSame([], new WarehouseQueryService($connection)->traceabilityEvents('tenant-id', 'serial', 'SN-001'));
    }

    public function testWarehouseTopologyIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(5))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        $result = new WarehouseQueryService($connection)->warehouseTopology('tenant-id');

        self::assertSame([], $result['bins']);
    }

    public function testWarehouseOverviewIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(2))->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        $result = new WarehouseQueryService($connection)->warehouseOverview('tenant-id');

        self::assertSame([], $result['warehouses']);
    }

    public function testWarehouseOccupancyIsTenantAndWarehouseScoped(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')->with(
            self::callback(static function (string $sql): bool {
                self::assertStringContainsString('WHERE l.tenant_id = :tenantId', $sql);
                self::assertStringContainsString('AND l.warehouse_id = :warehouseId', $sql);
                self::assertStringContainsString('blocked_quantity', $sql);
                self::assertStringContainsString('quality_quantity', $sql);

                return true;
            }),
            ['tenantId' => 'tenant-id', 'warehouseId' => 'warehouse-id'],
        )->willReturn([]);

        self::assertSame([], new WarehouseQueryService($connection)->warehouseOccupancy('tenant-id', 'warehouse-id'));
    }

    public function testStockQuotesTheReservedCursorAliasForMariaDb(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('AS `cursor`', $sql);

                    return true;
                }),
                self::isType('array'),
            )
            ->willReturn([]);

        $queries = new WarehouseQueryService($connection);

        self::assertSame([], $queries->stock('tenant-id', null, 200, null));
    }

    public function testStockMovementJournalIsTenantScopedAndNewestFirst(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
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

        self::assertSame([], new WarehouseQueryService($connection)->stockMovements('tenant-id', $criteria, 100, null));
    }
}
