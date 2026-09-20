<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class TransportEndpoint
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $code,
        public string $name,
        public string $adapterType,
        public string $address,
        public string $credentialEnv,
        public bool $active,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
        foreach ([$id, $tenantId, $createdBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A transport endpoint requires complete identifiers.');
            }
        }
        if (preg_match('/^[A-Z0-9][A-Z0-9_-]{1,39}$/', $code) !== 1 || trim($name) === '') {
            throw new \InvalidArgumentException('A transport endpoint requires a valid code and name.');
        }
        if (!in_array($adapterType, ['tcp_client', 'http_webservice'], true)) {
            throw new \InvalidArgumentException('The transport adapter type is unsupported.');
        }
        if ($adapterType === 'tcp_client' && preg_match('/^tcp:\/\/[^:\s]+:[1-9][0-9]{0,4}$/', $address) !== 1) {
            throw new \InvalidArgumentException('A TCP endpoint must use tcp://host:port.');
        }
        if ($adapterType === 'http_webservice' && (filter_var($address, FILTER_VALIDATE_URL) === false || !str_starts_with($address, 'https://'))) {
            throw new \InvalidArgumentException('A webservice endpoint must use HTTPS.');
        }
        if (preg_match('/^[A-Z][A-Z0-9_]{2,99}$/', $credentialEnv) !== 1) {
            throw new \InvalidArgumentException('A transport endpoint requires a credential environment reference.');
        }
    }
}
