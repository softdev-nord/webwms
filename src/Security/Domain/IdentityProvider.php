<?php

declare(strict_types=1);

namespace WebWMS\Security\Domain;

use InvalidArgumentException;

readonly class IdentityProvider
{
    /** @param list<string> $scopes */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $code,
        public string $name,
        public string $issuerUrl,
        public string $clientId,
        public string $clientSecretEnv,
        public array $scopes,
    ) {
        foreach ([$id, $tenantId, $code, $name, $issuerUrl, $clientId, $clientSecretEnv] as $value) {
            if (trim($value) === '') {
                throw new InvalidArgumentException('An identity provider requires complete configuration.');
            }
        }

        if (!str_starts_with($issuerUrl, 'https://') || $scopes === [] || !in_array('openid', $scopes, true)) {
            throw new InvalidArgumentException('An OIDC provider requires HTTPS and the openid scope.');
        }
    }
}
