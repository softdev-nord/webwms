<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Platform\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Platform\Application\PlatformControlService;

final class PlatformControlServiceTest extends TestCase
{
    public function testItRejectsUnknownResources(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service($this->createMock(Connection::class))->create('tenant', 'actor', 'unknown', [], new DateTimeImmutable());
    }

    public function testItRejectsAnInvalidTaskTransition(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(static fn (callable $callback): mixed => $callback($connection));
        $connection->method('fetchOne')->willReturn('planned');
        $connection->expects(self::never())->method('update');

        $this->expectException(DomainException::class);
        $this->service($connection)->transitionTask('tenant', 'actor', 'task', 'completed', new DateTimeImmutable());
    }

    public function testItRejectsOversizedMediaBeforePersistence(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('insert');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->captureMedia('tenant', 'actor', 'product', 'aggregate', 'photo.jpg', 'image/jpeg', str_repeat('x', 5_000_001), new DateTimeImmutable());
    }

    public function testItRejectsPrintRoutingWithoutMatchingRule(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn(false);

        $this->expectException(DomainException::class);
        $this->service($connection)->routePrinter('tenant', 'label', null, null, null);
    }

    public function testSearchTermsBelowTwoCharactersDoNotQueryPersistence(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('fetchAllAssociative');

        self::assertSame([], $this->service($connection)->search('tenant', 'x'));
    }

    private function service(Connection $connection): PlatformControlService
    {
        return new PlatformControlService($connection);
    }
}
