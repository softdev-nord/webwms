<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class StorageBinDefinition
{
    public function __construct(
        private string $code,
        private string $levelCode,
        private string $binCode,
        private string $locationType,
        private int $capacityQuantity,
    ) {
        foreach ([$code, $levelCode, $binCode] as $value) {
            if (preg_match('/^[A-Z0-9][A-Z0-9._-]{0,49}$/', $value) !== 1) {
                throw new InvalidArgumentException('Topology codes must contain uppercase letters, numbers, dots, underscores or hyphens.');
            }
        }
        if (!in_array($locationType, ['storage', 'receiving', 'shipping', 'quality', 'blocked'], true)) {
            throw new InvalidArgumentException('The storage location type is not supported.');
        }
        if ($capacityQuantity < 0) {
            throw new InvalidArgumentException('Storage bin capacity must not be negative.');
        }
    }

    public function code(): string
    {
        return mb_strtoupper(trim($this->code));
    }

    public function levelCode(): string
    {
        return mb_strtoupper(trim($this->levelCode));
    }

    public function binCode(): string
    {
        return mb_strtoupper(trim($this->binCode));
    }

    public function locationType(): string
    {
        return $this->locationType;
    }

    public function capacityQuantity(): int
    {
        return $this->capacityQuantity;
    }
}
