<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum StockBlockStatus: string
{
    case Open = 'open';
    case Reviewed = 'reviewed';
    case Released = 'released';

    public function canReview(): bool
    {
        return $this === self::Open;
    }

    public function canRelease(): bool
    {
        return $this === self::Reviewed;
    }
}
