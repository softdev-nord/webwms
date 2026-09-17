<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Tenant;

use InvalidArgumentException;

final readonly class TenantId
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    public function __construct(private string $value)
    {
        if (preg_match(self::UUID_PATTERN, $value) !== 1) {
            throw new InvalidArgumentException('A tenant ID must be a valid UUID.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }
}
