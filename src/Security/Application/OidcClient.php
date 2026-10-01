<?php

declare(strict_types=1);

namespace WebWMS\Security\Application;

use WebWMS\Security\Domain\IdentityProvider;

interface OidcClient
{
    public function authorization(IdentityProvider $provider, string $callbackUrl): OidcAuthorization;

    public function identity(
        IdentityProvider $provider,
        string $callbackUrl,
        string $code,
        string $nonce,
        string $codeVerifier,
    ): ExternalIdentity;
}
