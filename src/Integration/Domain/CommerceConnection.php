<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

readonly class CommerceConnection
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public string $channelType,
        public string $endpointUrl,
        public string $credentialEnv,
        public bool $active,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
        if (!in_array($channelType, ['shopware', 'amazon', 'ebay', 'generic'], true)) {
            throw new InvalidArgumentException('The commerce channel type is not supported.');
        }

        if (trim($name) === '' || mb_strlen($name) > 100) {
            throw new InvalidArgumentException('A commerce connection name must contain 1 to 100 characters.');
        }

        if (filter_var($endpointUrl, FILTER_VALIDATE_URL) === false || parse_url($endpointUrl, PHP_URL_SCHEME) !== 'https') {
            throw new InvalidArgumentException('A commerce endpoint must be a valid HTTPS URL.');
        }

        if (preg_match('/^[A-Z][A-Z0-9_]{2,100}$/', $credentialEnv) !== 1) {
            throw new InvalidArgumentException('A commerce credential reference must be an uppercase environment variable name.');
        }

        foreach ([$id, $tenantId, $createdBy] as $value) {
            if (trim($value) === '') {
                throw new InvalidArgumentException('A commerce connection requires complete identifiers.');
            }
        }
    }
}
