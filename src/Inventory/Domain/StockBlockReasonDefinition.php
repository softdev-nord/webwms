<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class StockBlockReasonDefinition
{
    public string $code;

    public string $name;

    public ?string $description;

    public function __construct(
        string $code,
        string $name,
        ?string $description,
        public bool $active
    ) {
        $this->code = mb_strtoupper(trim($code));
        $this->name = trim($name);
        $description = $description === null ? null : trim($description);
        $this->description = $description === '' ? null : $description;
        if ($this->code === '' || mb_strlen($this->code) > 30) {
            throw new InvalidArgumentException('The stock block reason code must contain 1 to 30 characters.');
        }
        if ($this->name === '' || mb_strlen($this->name) > 100) {
            throw new InvalidArgumentException('The stock block reason name must contain 1 to 100 characters.');
        }
        if ($this->description !== null && mb_strlen($this->description) > 255) {
            throw new InvalidArgumentException('The stock block reason description must not exceed 255 characters.');
        }
    }
}
