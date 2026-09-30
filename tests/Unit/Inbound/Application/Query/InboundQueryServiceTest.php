<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inbound\Application\Query;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Inbound\Application\Query\InboundQueryService;

class InboundQueryServiceTest extends TestCase
{
    public function testPlannedInboundWorklistIsRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'WHERE d.tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);

        self::assertSame([], new InboundQueryService($connection)->plannedInboundWorklist('tenant-id'));
    }

    public function testUnplannedReceiptsAreRestrictedToTheAuthenticatedTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'WHERE r.tenant_id = :tenantId')),
            ['tenantId' => 'tenant-id'],
        )->willReturn([]);
        self::assertSame([], new InboundQueryService($connection)->unplannedReceipts('tenant-id'));
    }
}
