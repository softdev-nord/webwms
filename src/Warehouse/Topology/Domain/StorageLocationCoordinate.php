<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Topology\Domain;

use InvalidArgumentException;

readonly class StorageLocationCoordinate
{
    public function __construct(
        private int $warehouseNumber,
        private int $levelNumber,
        private int $slotNumber,
        private int $depthNumber,
    ) {
        if ($warehouseNumber < 1 || $warehouseNumber > 999) {
            throw new InvalidArgumentException('The warehouse number must be between 1 and 999.');
        }
        foreach ([$levelNumber, $slotNumber, $depthNumber] as $value) {
            if ($value < 1 || $value > 9999) {
                throw new InvalidArgumentException('Level, slot and depth must be between 1 and 9999.');
            }
        }
    }

    public function value(): string
    {
        return sprintf('%03d%04d%04d%04d', $this->warehouseNumber, $this->levelNumber, $this->slotNumber, $this->depthNumber);
    }
}
