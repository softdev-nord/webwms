<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Stock\StockZoneDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockZoneService'
)]
readonly class StockZoneService
{
    public function __construct(
        private StockZoneDataHandler $stockZoneDataHandler,
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
