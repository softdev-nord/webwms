<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Shared\Domain\Model;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Shared\Domain\Event\DomainEvent;
use WebWMS\Shared\Domain\Model\AggregateRoot;

final class AggregateRootTest extends TestCase
{
    public function testItReleasesRecordedEventsOnlyOnce(): void
    {
        $occurredAt = new DateTimeImmutable('2026-09-17T00:00:00+00:00');
        $event = new TestDomainEvent($occurredAt);
        $aggregate = new TestAggregate();

        $aggregate->change($event);

        self::assertSame([$event], $aggregate->releaseEvents());
        self::assertSame([], $aggregate->releaseEvents());
    }
}
final class TestAggregate extends AggregateRoot
{
    public function change(DomainEvent $event): void
    {
        $this->recordThat($event);
    }
}

final readonly class TestDomainEvent implements DomainEvent
{
    public function __construct(private DateTimeImmutable $occurredAt)
    {
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
