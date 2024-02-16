<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLayoutEntity;
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

    public function getStockLayoutById(int $stockLayoutId): ?StockLayoutEntity
    {
        return $this->stockLayoutDataHandler->getStockLayoutById($stockLayoutId);
    }

    public function addStockLayout(StockLayoutEntity $stockLayoutEntity): void
    {
        $this->stockLayoutDataHandler->addStockLayout($stockLayoutEntity);
    }

    public function updateStockLayout(StockLayoutEntity $stockLayoutEntity): void
    {
        $this->stockLayoutDataHandler->updateStockLayout($stockLayoutEntity);
    }

    public function deleteStockLayout(StockLayoutEntity $stockLayoutEntity): void
    {
        $this->stockLayoutDataHandler->deleteStockLayout($stockLayoutEntity);
    }
}
