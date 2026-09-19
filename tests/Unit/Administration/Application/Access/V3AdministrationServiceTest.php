<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Application\Access;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Application\Access\V3AdministrationService;

final class V3AdministrationServiceTest extends TestCase
{
    public function testItStoresOnlyTheApiClientSecretHash(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('fetchOne')->willReturn(1);
        $connection->expects(self::once())
            ->method('insert')
            ->with(
                'wms_api_client',
                self::callback(static function (array $data): bool {
                    self::assertArrayHasKey('secret_hash', $data);
                    self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', (string) $data['secret_hash']);
                    self::assertArrayNotHasKey('secret', $data);

                    return true;
                }),
            );

        $result = (new V3AdministrationService($connection))->createApiClient(
            'tenant-id',
            'user-id',
            'ERP',
            ['inventory.stock.read'],
            new DateTimeImmutable('2026-09-19T12:00:00+00:00'),
        );

        self::assertStringStartsWith($result['id'] . '.', $result['credential']);
        self::assertGreaterThan(48, strlen($result['credential']));
    }

    public function testApiClientStatusUpdateIsTenantScoped(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('update')
            ->with('wms_api_client', ['active' => 0], ['id' => 'client-id', 'tenant_id' => 'tenant-id'])
            ->willReturn(1);

        (new V3AdministrationService($connection))->setApiClientActive('tenant-id', 'client-id', false);
    }
}
