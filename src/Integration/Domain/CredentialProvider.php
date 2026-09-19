<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface CredentialProvider
{
    public function secret(string $reference): string;
}
