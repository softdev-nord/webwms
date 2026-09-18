<?php

declare(strict_types=1);

namespace WebWMS\Administration\Infrastructure\Security;

use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use WebWMS\Administration\Domain\Access\PasswordHasher;
use WebWMS\Security\V3\SecurityUser;

final readonly class SymfonyPasswordHasher implements PasswordHasher
{
    public function __construct(
        private PasswordHasherFactoryInterface $factory
    ) {
    }

    public function hash(string $plainPassword): string
    {
        if (mb_strlen($plainPassword) < 12) {
            throw new InvalidArgumentException('A password must contain at least 12 characters.');
        }

        return $this->factory->getPasswordHasher(SecurityUser::class)->hash($plainPassword);
    }
}
