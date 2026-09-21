<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Fulfillment\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Fulfillment\Application\InternalTransportService;
use WebWMS\Inventory\Application\TransferStockHandler;
use WebWMS\Inventory\Domain\InventoryRepository;

final class InternalTransportServiceTest extends TestCase
{
    public function testMilkRunRequiresAtLeastTwoTenantStations(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchAllAssociative')->with(self::stringContains('r.tenant_id = :tenantId'), ['runId' => 'run-id', 'tenantId' => 'tenant-id'])->willReturn([
            ['sequence_number' => 1, 'location_id' => 'location-id'],
        ]);
        $inventory = $this->createMock(InventoryRepository::class);
        $inventory->expects(self::never())->method('transfer');
        $transferStock = new TransferStockHandler($inventory);

        $this->expectException(\DomainException::class);
        (new InternalTransportService($connection, $transferStock))->dispatchMilkRun('tenant-id', 'actor-id', 'run-id', new DateTimeImmutable());
    }
}
