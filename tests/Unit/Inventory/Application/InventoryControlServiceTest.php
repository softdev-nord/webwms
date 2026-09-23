<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\InventoryControlService;

final class InventoryControlServiceTest extends TestCase
{
    public function testHazardClassRequiresAllMasterData(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('insert');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->createHazardClass('tenant', 'actor', '', 'Entzündbar', '3', new DateTimeImmutable());
    }

    public function testBomRequiresAtLeastOneComponent(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('transactional');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->createBom('tenant', 'actor', 'product', 'BOM-1', '1', [], new DateTimeImmutable());
    }

    public function testLoadCarrierRejectsZeroMovement(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('transactional');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->bookLoadCarrier('tenant', 'actor', 'PARTNER', 'PAL', 0, 'REF', '', new DateTimeImmutable());
    }

    public function testForeignProductCannotBeClassified(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(static fn (callable $callback): mixed => $callback());
        $connection->method('fetchOne')->willReturn(false);
        $connection->expects(self::never())->method('insert');

        $this->expectException(DomainException::class);
        $this->service($connection)->classifyMaterial('tenant', 'actor', 'product', 'class', '1203', null, 'Kraftstoff', new DateTimeImmutable());
    }

    public function testNegativeStorageMaximumIsRejected(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('fetchOne');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->restrictStorage('tenant', 'actor', 'class', 'HZ-', true, -1, new DateTimeImmutable());
    }

    private function service(Connection $connection): InventoryControlService
    {
        return new InventoryControlService($connection);
    }
}
