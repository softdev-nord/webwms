<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\OutboundProcessService;
use WebWMS\Integration\Domain\OutboxRepository;

final class OutboundProcessServiceTest extends TestCase
{
    public function testItRejectsAnInvalidShippingRuleRange(): void
    {
        $service = $this->service($this->createMock(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $service->createShippingRule('tenant', 'standard', 'Standard', 'DHL', 'Paket', 5000, 1000, 10, 'actor', new DateTimeImmutable());
    }

    public function testItRejectsPackageWeightAboveConfiguredLimit(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn(31500);

        $this->expectException(DomainException::class);
        $this->service($connection)->assertPackageWeight('tenant', 31501);
    }

    public function testItAcceptsPackageWeightWithoutConstraint(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn(false);

        $this->service($connection)->assertPackageWeight('tenant', 50000);

        self::addToAssertionCount(1);
    }

    public function testItRejectsAnEmptyCancellationReasonBeforeAccessingPersistence(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service($this->createMock(Connection::class))->cancelOrder('tenant', 'order', ' ', 'actor', new DateTimeImmutable());
    }

    public function testItRejectsANonPositiveRuleSelectionWeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service($this->createMock(Connection::class))->selectShippingRule('tenant', 0);
    }

    public function testItRejectsATourWithoutStopsBeforeAccessingPersistence(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service($this->createMock(Connection::class))->createTour('tenant', 'T-1', 'DHL', 'Truck', 1000, new DateTimeImmutable(), [], 'actor', new DateTimeImmutable());
    }

    private function service(Connection $connection): OutboundProcessService
    {
        return new OutboundProcessService($connection, $this->createMock(OutboxRepository::class));
    }
}
