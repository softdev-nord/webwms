<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class Sku
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));

        if ($value === '' || strlen($value) > 64 || preg_match('/^[A-Z0-9][A-Z0-9._-]*$/', $value) !== 1) {
            throw new InvalidArgumentException('An SKU must contain 1 to 64 supported characters.');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
