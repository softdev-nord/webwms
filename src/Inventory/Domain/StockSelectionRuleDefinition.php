<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class StockSelectionRuleDefinition
{
    public string $code;

    public string $name;

    public function __construct(
        string $code,
        string $name,
        public StockSelectionStrategy $strategy,
        public int $priority,
        public bool $enabled,
    ) {
        $this->code = mb_strtoupper(trim($code));
        $this->name = trim($name);
        if ($this->code === '' || mb_strlen($this->code) > 50) {
            throw new InvalidArgumentException('The stock selection rule code must contain 1 to 50 characters.');
        }
        if ($this->name === '' || mb_strlen($this->name) > 100) {
            throw new InvalidArgumentException('The stock selection rule name must contain 1 to 100 characters.');
        }
        if ($this->priority < 1) {
            throw new InvalidArgumentException('The stock selection rule priority must be positive.');
        }
    }
}
