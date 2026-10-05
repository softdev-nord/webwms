<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Warehouse\Topology\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Warehouse\Topology\Application\WarehouseTopologyService;

class WarehouseTopologyServiceTest extends TestCase
{
    public function testUnknownTopologyResourceIsRejectedBeforeQuerying(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->never())->method('fetchAssociative');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->topologyEntry('tenant', 'unknown', 'id');
    }

    public function testForeignActorCannotUpdateTopology(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn(false);
        $connection->expects($this->never())->method('update');

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

    public function testCsvDryRunCalculatesEveryAddressableStorageLocationWithoutWriting(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(2))->method('fetchOne')->willReturn(1);
        $connection->expects($this->never())->method('insert');
        $csv = <<<'CSV'
"Lagernummer","Bezeichnung","Fachboden","Stellplatz","Tiefe","Lager-Model","Lager-Typ","Bezeichnung Lang","Letzte Änderung"
"101","Block","1","9","32","L2","BLL","Block","2019-03-01 00:00:00"
CSV;

        self::assertSame(
            ['rows' => 1, 'locations' => 288, 'created' => 0, 'skipped' => 0],
            $this->service($connection)->importCsv('tenant', 'warehouse', $csv, true, 'actor', new DateTimeImmutable()),
        );
    }

    private function service(Connection $connection): WarehouseTopologyService
    {
        return new WarehouseTopologyService($connection);
    }
}
