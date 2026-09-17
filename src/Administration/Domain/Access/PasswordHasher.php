<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;
}
