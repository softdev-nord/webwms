<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

interface UserAccountRepository
{
    public function existsByEmail(string $email): bool;

    public function save(UserAccount $user): void;
}
