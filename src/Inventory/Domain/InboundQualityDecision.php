<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum InboundQualityDecision: string
{
    case Accept = 'accept';
    case Block = 'block';

    public function stockStatus(): StockStatus
    {
        return $this === self::Accept ? StockStatus::Available : StockStatus::Blocked;
    }
}
