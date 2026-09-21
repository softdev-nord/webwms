<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

enum StockSelectionStrategy: string
{
    case Fifo = 'fifo';
    case Lifo = 'lifo';
    case Fefo = 'fefo';

    public static function fromInput(string $value): self
    {
        $strategy = self::tryFrom(mb_strtolower(trim($value)));
        if ($strategy === null) {
            throw new InvalidArgumentException('The stock selection strategy must be FIFO, LIFO or FEFO.');
        }

        return $strategy;
    }

    public function orderByClause(): string
    {
        return match ($this) {
            self::Fifo => 'first_received_at IS NULL ASC, first_received_at ASC, b.expires_at ASC',
            self::Lifo => 'first_received_at DESC, b.expires_at DESC',
            self::Fefo => 'b.expires_at IS NULL ASC, b.expires_at ASC, first_received_at ASC',
        };
    }
}
