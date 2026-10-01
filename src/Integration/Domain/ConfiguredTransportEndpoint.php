<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

readonly class ConfiguredTransportEndpoint
{
    public function __construct(
        public TransportEndpoint $endpoint,
        public ProtocolConfiguration $protocol,
    ) {
    }
}
