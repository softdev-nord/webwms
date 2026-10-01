<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\ConfiguredTransportEndpoint;

interface RuntimeIntegrationTransport
{
    /** @param array<string, mixed> $payload */
    public function deliver(ConfiguredTransportEndpoint $target, string $messageId, array $payload): void;
}
