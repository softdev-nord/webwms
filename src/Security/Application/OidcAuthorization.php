<?php

declare(strict_types=1);

namespace WebWMS\Security\Application;

readonly class OidcAuthorization
{
    public function __construct(
        public string $url,
        public string $state,
        public string $nonce,
        public string $codeVerifier,
    ) {
    }
}
