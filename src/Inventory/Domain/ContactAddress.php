<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

readonly class ContactAddress
{
    public function __construct(
        private string $name,
        private string $street,
        private string $postalCode,
        private string $city,
        private string $countryCode,
    ) {
        if (trim($name) === '' || mb_strlen($name) > 160) {
            throw new InvalidArgumentException('An address name must contain 1 to 160 characters.');
        }

        if (trim($street) === '' || mb_strlen($street) > 255) {
            throw new InvalidArgumentException('A street must contain 1 to 255 characters.');
        }

        if (trim($postalCode) === '' || mb_strlen($postalCode) > 32) {
            throw new InvalidArgumentException('A postal code must contain 1 to 32 characters.');
        }

        if (trim($city) === '' || mb_strlen($city) > 120) {
            throw new InvalidArgumentException('A city must contain 1 to 120 characters.');
        }

        if (preg_match('/^[A-Za-z]{2}$/', trim($countryCode)) !== 1) {
            throw new InvalidArgumentException('A country code must contain two ISO 3166-1 alpha-2 letters.');
        }
    }

    public function name(): string
    {
        return trim($this->name);
    }

    public function street(): string
    {
        return trim($this->street);
    }

    public function postalCode(): string
    {
        return trim($this->postalCode);
    }

    public function city(): string
    {
        return trim($this->city);
    }

    public function countryCode(): string
    {
        return mb_strtoupper(trim($this->countryCode));
    }
}
