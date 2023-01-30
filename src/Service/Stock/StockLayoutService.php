<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use WebWMS\Service\DataHandlers\Stock\StockLayoutDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLayoutService
 */
class StockLayoutService
{
    public function __construct(
        private StockLayoutDataHandler $stockLayoutDataHandler
    ) {
    }

    public function getStockLayout(): array
    {
        return $this->stockLayoutDataHandler->getStockLayout();
    }
}
