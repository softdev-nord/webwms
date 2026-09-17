<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Site;

use InvalidArgumentException;

final readonly class SiteCode
{
    private const CODE_PATTERN = '/^[A-Z0-9][A-Z0-9_-]{1,19}$/';

    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));

        if (preg_match(self::CODE_PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(
                'A site code must contain 2 to 20 letters, numbers, underscores or hyphens.',
            );
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
