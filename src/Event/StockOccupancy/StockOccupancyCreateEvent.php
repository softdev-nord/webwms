<?php

declare(strict_types=1);

namespace WebWMS\Event\StockOccupancy;

use WebWMS\Entity\StockOccupancy;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Stock\StockOccupancyService;

#[ClassInformation(
    package: 'WebWMS\Event\StockOccupancy',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockOccupancyCreateEvent'
)]
class StockOccupancyCreateEvent
{
    final public const EVENT_NAME = 'stock_occupancy.create';

    public function __construct(
        private readonly StockOccupancyService $stockOccupancyService
    ) {
    }

    public function createStockOccupancy(int $id): void
    {
        $stockOccupancy = $this->stockOccupancyService->getStockOccupancyById($id);

        if ($stockOccupancy instanceof StockOccupancy) {
            $this->stockOccupancyService->addStockOccupancy($stockOccupancy);
        }
    }
}
