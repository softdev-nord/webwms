<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Infrastructure\Persistence;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Integration\Domain\OutboxRepository;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\OutboundOrderRelease;
use WebWMS\Inventory\Infrastructure\Persistence\DbalInventoryRepository;

final class DbalInventoryRepositoryTest extends TestCase
{
    public function testItReleasesCreatedAndImportedOutboundOrders(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('transactional')
            ->willReturnCallback(static fn (callable $operation): mixed => $operation($connection));
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString("status IN ('created', 'imported')", $sql);

                    return true;
                }),
                self::isType('array'),
            )
            ->willReturn(['id' => $this->id('001'), 'order_number' => 'ORDER-100']);
        $connection->expects(self::once())->method('fetchOne')->willReturn(1);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([[
                'id' => $this->id('002'),
                'product_id' => $this->id('003'),
                'requested_quantity' => 2,
            ]]);
        $connection->expects(self::once())->method('insert')->willReturn(1);
        $connection->expects(self::exactly(2))->method('update')->willReturn(1);

        $repository = new DbalInventoryRepository($connection, $this->createMock(OutboxRepository::class));
        $result = $repository->releaseOutboundOrder(new OutboundOrderRelease(
            new InventoryId($this->id('001')),
            new TenantId($this->id('004')),
            [$this->id('002') => new InventoryId($this->id('005'))],
            new UserId($this->id('006')),
            new DateTimeImmutable('2026-09-19 18:00:00'),
        ));

        self::assertSame('released', $result->status);
        self::assertSame(1, $result->lineCount);
        self::assertSame(1, $result->reservationCount);
    }

    private function id(string $suffix): string
    {
        return '018f6b7f-75d2-7c4e-8c33-31f91b1cf' . $suffix;
    }
}
