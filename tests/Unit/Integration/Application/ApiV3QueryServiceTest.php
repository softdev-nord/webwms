<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\ApiV3QueryService;

final class ApiV3QueryServiceTest extends TestCase
{
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
}
