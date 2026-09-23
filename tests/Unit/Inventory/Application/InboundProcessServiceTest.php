<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\InboundProcessService;
use WebWMS\Inventory\Application\PostStockHandler;
use WebWMS\Inventory\Domain\InventoryRepository;

final class InboundProcessServiceTest extends TestCase
{
    public function testItRejectsAnEmptyAttachment(): void
    {
        $service = $this->service($this->createMock(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $service->attach('tenant', 'inbound_receipt', 'receipt', 'photo', 'damage.jpg', 'image/jpeg', '', 'actor', new DateTimeImmutable());
    }

    public function testItRejectsAnUnsupportedLabelType(): void
    {
        $service = $this->service($this->createMock(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $service->requestLabel('tenant', 'inbound_receipt', 'receipt', 'unknown', 1, 'actor', new DateTimeImmutable());
    }

    public function testItQueuesAValidatedLabel(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('insert')->with('wms_inbound_label_job', self::callback(static fn (array $row): bool => $row['label_type'] === 'product' && $row['copies'] === 2 && $row['status'] === 'queued'));

        $id = $this->service($connection)->requestLabel('tenant', 'inbound_receipt', 'receipt', 'product', 2, 'actor', new DateTimeImmutable());

        self::assertNotSame('', $id);
    }

    private function service(Connection $connection): InboundProcessService
    {
        return new InboundProcessService($connection, new PostStockHandler($this->createMock(InventoryRepository::class)));
    }
}
