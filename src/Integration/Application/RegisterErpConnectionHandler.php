<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\ErpConnection;
use WebWMS\Integration\Domain\ErpConnectionRepository;

final readonly class RegisterErpConnectionHandler
{
    public function __construct(
        private ErpConnectionRepository $connections
    ) {
    }

    public function __invoke(RegisterErpConnectionCommand $command): ErpConnection
    {
        $connection = new ErpConnection(
            $command->id,
            $command->tenantId,
            trim($command->name),
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
