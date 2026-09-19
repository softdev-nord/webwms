<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

final readonly class OutboxPublishReport
{
    public function __construct(
        public int $claimed,
        public int $published,
        public int $retryScheduled,
        public int $deadLettered
    ) {
    }
}
