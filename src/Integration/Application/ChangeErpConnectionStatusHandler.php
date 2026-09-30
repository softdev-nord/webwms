<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\ErpConnectionRepository;

readonly class ChangeErpConnectionStatusHandler
{
    public function __construct(
        private ErpConnectionRepository $connections
    ) {
    }

    public function __invoke(ChangeErpConnectionStatusCommand $command): void
    {
        $this->connections->changeStatus(
            $command->connectionId,
            $command->tenantId,
            $command->active,
            $command->changedBy,
            $command->changedAt,
        );
    }
}
