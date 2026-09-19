<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\CarrierConnection;
use WebWMS\Integration\Domain\CarrierConnectionRepository;

final readonly class RegisterCarrierConnectionHandler
{
    public function __construct(
        private CarrierConnectionRepository $connections
    ) {
    }

    public function __invoke(RegisterCarrierConnectionCommand $command): CarrierConnection
    {
        $connection = new CarrierConnection(
            $command->id,
            $command->tenantId,
            trim($command->name),
            mb_strtoupper(trim($command->carrierCode)),
            rtrim($command->endpointUrl, '/'),
            $command->credentialEnv,
            $command->active,
            $command->createdBy,
            $command->createdAt,
        );
        $this->connections->add($connection);

        return $connection;
    }
}
