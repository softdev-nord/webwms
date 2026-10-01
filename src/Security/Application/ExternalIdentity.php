<?php

declare(strict_types=1);

namespace WebWMS\Security\Application;

readonly class ExternalIdentity
{
    public function __construct(
        public string $subject,
        public string $email,
    ) {
    }
}
