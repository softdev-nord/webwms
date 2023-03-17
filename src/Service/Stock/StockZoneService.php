<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLayout;
use WebWMS\Entity\StockZone;
use WebWMS\Service\DataHandlers\Stock\StockZoneDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneService
 */
class StockZoneService
{
    public function __construct(
        private StockZoneDataHandler $stockZoneDataHandler
    ) {
    }

    public function getAllStockZones(): JsonResponse
    {
        return $this->stockZoneDataHandler->getAllStockZones();
    }

    public function getStockZoneById(int $stockZoneId): ?StockZone
    {
        return $this->stockZoneDataHandler->getStockZoneById($stockZoneId);
    }

    public function addStockZone(StockZone $stockZone): void
    {
        $this->stockZoneDataHandler->addStockZone($stockZone);
    }

    public function updateStockZone(StockZone $stockZone): void
    {
        $this->stockZoneDataHandler->updateStockZone($stockZone);
    }

    public function deleteStockZone(StockZone $stockZone): void
    {
        $this->stockZoneDataHandler->deleteStockZone($stockZone);
    }
}
