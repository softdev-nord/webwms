<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\CarrierConnectionRepository;

final readonly class ChangeCarrierConnectionStatusHandler
{
    public function __construct(
        private CarrierConnectionRepository $connections
    ) {
    }

    public function __invoke(ChangeCarrierConnectionStatusCommand $command): void
    {
        $this->connections->changeStatus($command->id, $command->tenantId, $command->active, $command->actorId, $command->at);
    }
}
