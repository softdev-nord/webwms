<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class Printer
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public string $endpointUrl,
        public string $credentialEnv,
        public bool $active,
        public string $createdBy,
        public DateTimeImmutable $createdAt
    ) {
        foreach ([$id, $tenantId, $createdBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A printer requires complete identifiers.');
            }
        }
        if (trim($name) === '' || mb_strlen($name) > 100) {
            throw new \InvalidArgumentException('A printer name must contain 1 to 100 characters.');
        }
        if (filter_var($endpointUrl, FILTER_VALIDATE_URL) === false
            || parse_url($endpointUrl, PHP_URL_SCHEME) !== 'https'
            || parse_url($endpointUrl, PHP_URL_USER) !== null
            || parse_url($endpointUrl, PHP_URL_QUERY) !== null
            || parse_url($endpointUrl, PHP_URL_FRAGMENT) !== null
            || mb_strlen($endpointUrl) > 500) {
            throw new \InvalidArgumentException('A printer endpoint must be a valid HTTPS URL.');
        }
        if (preg_match('/^[A-Z][A-Z0-9_]{2,100}$/', $credentialEnv) !== 1) {
            throw new \InvalidArgumentException('A printer credential reference must be an uppercase environment variable name.');
        }
    }
}
