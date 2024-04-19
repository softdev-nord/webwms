<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\StockOccupancyEntity;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyService
 */
class StockOccupancyService
{
    public function __construct(
        private readonly StockOccupancyDataHandler $stockOccupancyDataHandler,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(StockOccupancyEntity $stockOccupancyEntity): void
    {
        $this->stockOccupancyDataHandler->save($stockOccupancyEntity);
    }

    public function delete(StockOccupancyEntity $stockOccupancyEntity): void
    {
        $this->stockOccupancyDataHandler->delete($stockOccupancyEntity);
    }

    public function getStockOccupancyById(int $id): ?StockOccupancyEntity
    {
        return $this->stockOccupancyDataHandler->getStockOccupancyById($id);
    }

    public function getStockOccupancyByStockLocationId(int $id): ?StockOccupancyEntity
    {
        return $this->stockOccupancyDataHandler->getStockOccupancyById($id);
    }

    /**
     * @throws Exception
     */
    public function getAllStockOccupancy(): JsonResponse
    {
        return $this->stockOccupancyDataHandler->getAllStockOccupancy();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getStockOccupancyByCoordinate(Request $request): array
    {
        $stockLocationCoordinate = $request->attributes->get('stock_location_coordinate');
        $stockOccupancyDetail = [];
        $stockOccupancies = $this->stockOccupancyDataHandler->getStockOccupancyByCoordinate($stockLocationCoordinate);

        foreach ($stockOccupancies as $stockOccupancy) {
            if ($stockOccupancy['stock_coordinate'] === $stockLocationCoordinate) {
                $stockOccupancyDetail[] = $stockOccupancy;
            }
        }

        return $stockOccupancyDetail;
    }

    /**
     * @throws Exception
     * @return array<mixed>
     */
    public function getAllStockOccupancyByLn(int $stockLocationLn): array
    {
        return $this->stockOccupancyDataHandler->getStockOccupancy($stockLocationLn);
    }

    /**
     * @throws Exception
     * @return array<int|mixed|string>
     */
    public function getStockOccupancyByArticleNr(int $articleNr): array
    {
        return $this->stockOccupancyDataHandler->getStockOccupancyByArticleNr($articleNr);
    }

    /**
     * @throws Exception
     * @return array<int|mixed|string>
     */
    public function getStockOccupancyByArticleId(int $articleId): array
    {
        return $this->stockOccupancyDataHandler->getStockOccupancyByArticleId($articleId);
    }

    public function updateStockOccupancy(StockOccupancyEntity $stockOccupancyEntity): void
    {
        $stockOccupancyEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockOccupancyEntity);
    }

    public function deleteArticle(StockOccupancyEntity $stockOccupancyEntity): void
    {
        $this->delete($stockOccupancyEntity);
    }

    public function checkStockLocationFreeSpace(int $stockLocationId, string $leQuantity): float
    {
        $stockOccupancy = $this->stockOccupancyDataHandler->getStockOccupancyByStockLocationId($stockLocationId);

        // dd($stockOccupancy);

        if ($stockOccupancy instanceof StockOccupancyEntity && $stockOccupancy->getInStock() !== null && $stockOccupancy->getIncomingStock() !== null && $stockOccupancy->getReservedStock() !== null) {
            $sumInventory = $stockOccupancy->getInStock() + $stockOccupancy->getIncomingStock() + $stockOccupancy->getReservedStock();
            // dd((float) $leQuantity);
            if ($sumInventory != null && $sumInventory < $leQuantity) {
                return $sumInventory;
            }
        }

        return 0;
    }
}
