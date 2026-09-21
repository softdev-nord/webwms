<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class SpecialStockTypeDefinition
{
    public string $code;

    public string $name;

    public string $kind;

    public function __construct(
        string $code,
        string $name,
        string $kind,
        public bool $allocatable
    ) {
        $this->code = mb_strtoupper(trim($code));
        $this->name = trim($name);
        $this->kind = mb_strtolower(trim($kind));
        if ($this->code === '' || mb_strlen($this->code) > 30) {
            throw new InvalidArgumentException('The special stock code must contain 1 to 30 characters.');
        }
        if ($this->name === '' || mb_strlen($this->name) > 100) {
            throw new InvalidArgumentException('The special stock name must contain 1 to 100 characters.');
        }
        if (!in_array($this->kind, ['owner', 'status', 'special'], true)) {
            throw new InvalidArgumentException('The special stock classification kind is invalid.');
        }
    }
}
