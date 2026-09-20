<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class AutomationDevice
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $code,
        public string $name,
        public string $type,
        public string $endpointUrl,
        public string $credentialEnv,
        public bool $active,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
        foreach ([$id, $tenantId, $createdBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('An automation device requires complete identifiers.');
            }
        }
        if (preg_match('/^[A-Z0-9][A-Z0-9_-]{1,39}$/', $code) !== 1) {
            throw new \InvalidArgumentException('An automation device code must contain 2 to 40 uppercase characters.');
        }
        if (trim($name) === '' || mb_strlen($name) > 100) {
            throw new \InvalidArgumentException('An automation device name must contain 1 to 100 characters.');
        }
        if (!in_array($type, ['storage_lift', 'paternoster', 'logimat'], true)) {
            throw new \InvalidArgumentException('The automation device type is unsupported.');
        }
        if (filter_var($endpointUrl, FILTER_VALIDATE_URL) === false || !str_starts_with($endpointUrl, 'https://')) {
            throw new \InvalidArgumentException('An automation device endpoint must be an HTTPS URL.');
        }
        if (preg_match('/^[A-Z][A-Z0-9_]{2,99}$/', $credentialEnv) !== 1) {
            throw new \InvalidArgumentException('An automation device requires a credential environment reference.');
        }
    }
}
