<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

final readonly class ProtocolConfiguration
{
    public function __construct(
        public string $endpointId,
        public string $protocol,
        public string $framing,
        public int $connectTimeoutMs,
        public int $readTimeoutMs,
    ) {
        if (trim($endpointId) === '') {
            throw new \InvalidArgumentException('A protocol configuration requires an endpoint.');
        }
        if (!in_array($protocol, ['raw_tcp', 'rest_json', 'soap_xml'], true)) {
            throw new \InvalidArgumentException('The integration protocol is unsupported.');
        }
        if (!in_array($framing, ['none', 'newline', 'stx_etx', 'http'], true)) {
            throw new \InvalidArgumentException('The message framing is unsupported.');
        }
        if (($protocol === 'raw_tcp' && $framing === 'http') || ($protocol !== 'raw_tcp' && $framing !== 'http')) {
            throw new \InvalidArgumentException('The framing does not match the selected protocol.');
        }
        if ($connectTimeoutMs < 100 || $connectTimeoutMs > 60000 || $readTimeoutMs < 100 || $readTimeoutMs > 300000) {
            throw new \InvalidArgumentException('Transport timeouts are outside the supported range.');
        }
    }
}
