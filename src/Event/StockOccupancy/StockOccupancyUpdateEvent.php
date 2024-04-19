<?php

declare(strict_types=1);

namespace WebWMS\Event\StockOccupancy;

use WebWMS\Entity\StockOccupancyEntity;
use WebWMS\Service\Stock\StockOccupancyService;

/**
 * @package:    WebWMS\Event\StockOccupancy
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyUpdateEvent
 */
class StockOccupancyUpdateEvent
{
    final public const EVENT_NAME = 'stock_occupancy.update';

    public function __construct(
        private readonly StockOccupancyService $stockOccupancyService
    ) {
    }

    public function updateStockOccupancy(int $id): void
    {
        $stockOccupancy = $this->stockOccupancyService->getStockOccupancyById($id);

        if ($stockOccupancy instanceof StockOccupancyEntity) {
            $this->stockOccupancyService->save($stockOccupancy);
        }
    }
}
