<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum StockStatus: string
{
    case Available = 'available';
    case Blocked = 'blocked';
    case QualityInspection = 'quality_inspection';
}
