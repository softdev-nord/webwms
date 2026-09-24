<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Platform\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Platform\Application\GapClosureService;

final class GapClosureServiceTest extends TestCase
{
    public function testItExposesAllGapClosureCapabilities(): void
    {
        self::assertCount(18, GapClosureService::RESOURCES);
        self::assertCount(13, GapClosureService::WORKFLOWS);
    }

    public function testItRejectsUnknownConfigurationResourcesBeforePersistence(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('insert');

        $this->expectException(InvalidArgumentException::class);
        $this->service($connection)->saveConfiguration('tenant', 'actor', 'unknown', null, 'CODE', 'Name', [], true, new DateTimeImmutable());
    }

    public function testItRejectsInvalidWorkflowTransitions(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(static fn (callable $callback): mixed => $callback($connection));
        $connection->method('fetchAssociative')->willReturn(['workflow_type' => 'invoice', 'status' => 'draft']);
        $connection->expects(self::never())->method('update');

        $this->expectException(DomainException::class);
        $this->service($connection)->transition('tenant', 'actor', 'invoice', 'paid', new DateTimeImmutable());
    }

    public function testItProvidesOnlyConfiguredNextStates(): void
    {
        $service = $this->service($this->createMock(Connection::class));

        self::assertSame(['approved', 'cancelled'], $service->allowedTransitions('invoice', 'draft'));
        self::assertSame([], $service->allowedTransitions('invoice', 'paid'));
        self::assertSame([], $service->allowedTransitions('unknown', 'draft'));
    }

    private function service(Connection $connection): GapClosureService
    {
        return new GapClosureService($connection);
    }
}
