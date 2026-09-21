<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Fulfillment\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Fulfillment\Application\AdvancedPickingService;

final class AdvancedPickingServiceTest extends TestCase
{
    public function testScanValidatesEveryControlledDimensionAndAuditsSuccess(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(static fn (callable $callback): mixed => $callback($connection));
        $connection->expects(self::once())->method('fetchAssociative')->with(self::stringContains('pl.tenant_id = :tenantId'), ['taskId' => 'task-id', 'tenantId' => 'tenant-id'])->willReturn(['id' => 'task-id', 'quantity' => 2, 'batch_number' => 'LOT-1', 'serial_number' => 'SER-1', 'sku' => 'SKU-1', 'location_code' => 'A-01']);
        $connection->expects(self::once())->method('insert')->with('wms_pick_scan_event', self::callback(static fn (array $data): bool => $data['result'] === 'accepted' && $data['tenant_id'] === 'tenant-id'))->willReturn(1);

        $result = (new AdvancedPickingService($connection))->validateScan('tenant-id', 'user-id', 'task-id', 'A-01', 'SKU-1', 'LOT-1', 'SER-1', 2, new DateTimeImmutable());

        self::assertTrue($result['valid']);
    }

    public function testScanRejectsWrongLocationWithoutConfirmingTheTask(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(static fn (callable $callback): mixed => $callback($connection));
        $connection->method('fetchAssociative')->willReturn(['id' => 'task-id', 'quantity' => 1, 'batch_number' => null, 'serial_number' => null, 'sku' => 'SKU-1', 'location_code' => 'A-01']);
        $connection->expects(self::once())->method('insert')->with('wms_pick_scan_event', self::callback(static fn (array $data): bool => $data['result'] === 'rejected'))->willReturn(1);

        $result = (new AdvancedPickingService($connection))->validateScan('tenant-id', 'user-id', 'task-id', 'B-99', 'SKU-1', null, null, 1, new DateTimeImmutable());

        self::assertFalse($result['valid']);
    }

    public function testSingleOrderWaveRejectsMultiplePickLists(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('transactional');

        $this->expectException(\InvalidArgumentException::class);
        (new AdvancedPickingService($connection))->createWave('tenant-id', 'user-id', 'wave-1', 'Wave 1', 'single_order', 'manual', null, 50, null, ['pick-1', 'pick-2'], new DateTimeImmutable());
    }
}
