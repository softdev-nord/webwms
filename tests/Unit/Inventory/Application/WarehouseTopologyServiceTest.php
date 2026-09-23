<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\WarehouseTopologyService;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;

final class WarehouseTopologyServiceTest extends TestCase
{
    public function testUnknownTopologyResourceIsRejectedBeforeQuerying(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('fetchAssociative');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->topologyEntry('tenant', 'unknown', 'id');
    }

    public function testForeignActorCannotUpdateTopology(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn(false);
        $connection->expects(self::never())->method('update');

        $this->expectException(InventoryReferenceNotFoundException::class);
        $this->service($connection)->updateTopologyEntry('tenant', 'foreign-user', 'site', 'site', ['code' => 'S01', 'name' => 'Standort', 'timezone' => 'Europe/Berlin', 'status' => 'active'], new DateTimeImmutable());
    }

    public function testMissingTopologyEntryReturnsNotFound(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchAssociative')->willReturn(false);

        $this->expectException(InventoryReferenceNotFoundException::class);
        $this->service($connection)->topologyEntry('tenant', 'site', 'missing');
    }

    private function service(Connection $connection): WarehouseTopologyService
    {
        return new WarehouseTopologyService($connection);
    }
}
