<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Outbound\Application\Query;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Outbound\Application\Query\OutboundQueryService;

class OutboundQueryServiceTest extends TestCase
{
    public function testAvailableStockExcludesBlockedStatus(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')->with(
            self::callback(static function (string $sql): bool {
                self::assertStringContainsString("b.stock_status = 'available'", $sql);

                return true;
            }),
            ['tenantId' => 'tenant-id', 'productId' => 'product-id'],
        )->willReturn([]);

        self::assertSame([], new OutboundQueryService($connection)->availableStockForProduct('tenant-id', 'product-id'));
    }

    public function testOutboundOrderListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE o.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new OutboundQueryService($connection)->outboundOrders('tenant-id'));
    }

    public function testPickListQueueIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE l.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new OutboundQueryService($connection)->pickLists('tenant-id'));
    }

    public function testPackingQueueIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE p.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new OutboundQueryService($connection)->packingOrders('tenant-id'));
    }

    public function testShippingQueueIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE s.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new OutboundQueryService($connection)->shipments('tenant-id'));
    }

    public function testLoadingManifestListIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('WHERE m.tenant_id = :tenantId', $sql);

                    return true;
                }),
                ['tenantId' => 'tenant-id'],
            )
            ->willReturn([]);

        self::assertSame([], new OutboundQueryService($connection)->loadingManifests('tenant-id'));
    }

    public function testAvailableLoadingShipmentsAreLabelledAndUnassigned(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
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

        self::assertSame([], new OutboundQueryService($connection)->shipmentsAvailableForLoading('tenant-id'));
    }
}
