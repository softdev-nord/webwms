<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Security;

use WebWMS\Integration\Domain\CredentialProvider;

final class EnvironmentCredentialProvider implements CredentialProvider
{
    public function secret(string $reference): string
    {
        $secret = getenv($reference);
        if (!is_string($secret) || $secret === '') {
            throw new \RuntimeException(sprintf('ERP credential reference "%s" is not configured.', $reference));
        }

        return $secret;
    }
}
