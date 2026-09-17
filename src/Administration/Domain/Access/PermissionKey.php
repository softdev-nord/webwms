<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use InvalidArgumentException;

final readonly class PermissionKey
{
    private const KEY_PATTERN = '/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*){2,4}$/';

    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));

        if (preg_match(self::KEY_PATTERN, $value) !== 1 || strlen($value) > 100) {
            throw new InvalidArgumentException(
                'A permission key must contain 3 to 5 dot-separated lowercase segments.',
            );
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
