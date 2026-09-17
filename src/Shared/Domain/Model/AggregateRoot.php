<?php

declare(strict_types=1);

namespace WebWMS\Shared\Domain\Model;

use WebWMS\Shared\Domain\Event\DomainEvent;

abstract class AggregateRoot
{
    /** @var list<DomainEvent> */
    private array $recordedEvents = [];

    /**
     * @return list<DomainEvent>
     */
    final public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    final protected function recordThat(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
