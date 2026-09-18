<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum ReturnQualityDecision: string
{
    case Restock = 'restock';
    case Quarantine = 'quarantine';

    public function stockStatus(): StockStatus
    {
        return match ($this) {
            self::Restock => StockStatus::Available,
            self::Quarantine => StockStatus::Blocked,
        };
    }
}
