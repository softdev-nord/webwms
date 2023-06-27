<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLayout;
use WebWMS\Service\DataHandlers\Stock\StockLayoutDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutService
 */
class StockLayoutService
{
    public function __construct(
        private readonly StockLayoutDataHandler $stockLayoutDataHandler
    ) {
    }

    public function getAllStockLayouts(): JsonResponse
    {
        return $this->stockLayoutDataHandler->getAllStockLayouts();
    }

    public function getStockLayoutById(int $stockLayoutId): ?StockLayout
    {
        return $this->stockLayoutDataHandler->getStockLayoutById($stockLayoutId);
    }

    public function addStockLayout(StockLayout $stockLayout): void
    {
        $this->stockLayoutDataHandler->addStockLayout($stockLayout);
    }

    public function updateStockLayout(StockLayout $stockLayout): void
    {
        $this->stockLayoutDataHandler->updateStockLayout($stockLayout);
    }

    public function deleteStockLayout(StockLayout $stockLayout): void
    {
        $this->stockLayoutDataHandler->deleteStockLayout($stockLayout);
    }
}
