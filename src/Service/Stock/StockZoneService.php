<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZoneEntity;
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
        private readonly StockZoneDataHandler $stockZoneDataHandler
    ) {
    }

    public function getAllStockZones(): JsonResponse
    {
        return $this->stockZoneDataHandler->getAllStockZones();
    }

    public function getStockZoneById(int $stockZoneId): ?StockZoneEntity
    {
        return $this->stockZoneDataHandler->getStockZoneById($stockZoneId);
    }

    public function addStockZone(StockZoneEntity $stockZoneEntity): void
    {
        $this->stockZoneDataHandler->addStockZone($stockZoneEntity);
    }

    public function updateStockZone(StockZoneEntity $stockZoneEntity): void
    {
        $this->stockZoneDataHandler->updateStockZone($stockZoneEntity);
    }

    public function deleteStockZone(StockZoneEntity $stockZoneEntity): void
    {
        $this->stockZoneDataHandler->deleteStockZone($stockZoneEntity);
    }
}
