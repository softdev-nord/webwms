<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Application\AdministrationWorkspaceService;

final class AdministrationWorkspaceServiceTest extends TestCase
{
    public function testNumberAllocationIsTenantScopedLockedAndAtomic(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('transactional')->willReturnCallback(static fn (callable $callback): mixed => $callback($connection));
        $connection->expects(self::once())->method('fetchAssociative')->with(
            self::callback(static fn (string $sql): bool => str_contains($sql, 'tenant_id = :tenantId') && str_contains($sql, 'FOR UPDATE')),
            ['tenantId' => 'tenant-id', 'code' => 'outbound'],
        )->willReturn(['id' => 'range-id', 'prefix' => 'AU-', 'suffix' => '', 'padding' => 6, 'next_value' => 42, 'maximum_value' => 999999]);
        $connection->expects(self::once())->method('update')->with('wms_number_range', self::callback(static fn (array $data): bool => $data['next_value'] === 43), ['id' => 'range-id', 'tenant_id' => 'tenant-id'])->willReturn(1);
        $connection->expects(self::once())->method('insert')->with('wms_administration_event', self::callback(static fn (array $event): bool => $event['event_type'] === 'number_allocated' && $event['tenant_id'] === 'tenant-id'))->willReturn(1);

        $number = (new AdministrationWorkspaceService($connection))->nextNumber('tenant-id', 'actor-id', 'outbound', new DateTimeImmutable('2026-09-21 12:00:00'));

        self::assertSame('AU-000042', $number);
    }

    public function testContextRejectsBusinessPartnerFromAnotherTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchOne')->with(
            self::stringContains('wms_business_partner'),
            ['id' => 'partner-id', 'tenantId' => 'tenant-id'],
        )->willReturn(false);
        $connection->expects(self::never())->method('insert');

        $this->expectException(\InvalidArgumentException::class);
        (new AdministrationWorkspaceService($connection))->create('tenant-id', 'actor-id', 'context', [
            'business_partner_id' => 'partner-id', 'code' => 'customer-a', 'name' => 'Customer A', 'active' => true,
        ], new DateTimeImmutable());
    }

    public function testDeploymentRejectsUnknownInfrastructureDrivers(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('transactional');

        $this->expectException(\InvalidArgumentException::class);
        (new AdministrationWorkspaceService($connection))->configureDeployment('tenant-id', 'actor-id', [
            'deployment_mode' => 'saas', 'public_url' => 'https://wms.example.test', 'storage_driver' => 'ftp',
            'queue_transport' => 'rabbitmq', 'release_channel' => 'stable',
        ], new DateTimeImmutable());
    }
}
