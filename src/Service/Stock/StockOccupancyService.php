<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockOccupancyService
 */
class StockOccupancyService
{
    public function __construct(
        private StockOccupancyDataHandler $stockOccupancyDataHandler
    ) {
    }

    /**
     * @throws Exception
     */
    public function getAllStockOccupancy(): JsonResponse
    {
        return $this->stockOccupancyDataHandler->getAllStockOccupancy();
    }

    /**
     * @return array<string>
     * @throws Exception
     */
    public function getStockOccupancyByCoordinate(Request $request): array
    {
        $stockLocationCoordinate = $request->attributes->get('stock_location_coordinate');
        $stockOccupancyDetail = [];
        $getAllStockOccupancy = $this->getAllStockOccupancy();
        $stockOccupancies = json_decode((string) $getAllStockOccupancy->getContent(), true);

        foreach ($stockOccupancies as $stockOccupancy) {
            if ($stockOccupancy['koordinate'] === $stockLocationCoordinate) {
                $stockOccupancyDetail[] = $stockOccupancy;
            }
        }

        return $stockOccupancyDetail;
    }

    /**
     * @return array<int|mixed>
     * @throws Exception
     */
    public function getAllStockOccupancyByLn(int $stockLocationLn): array
    {
        return $this->stockOccupancyDataHandler->getStockOccupancy($stockLocationLn);
    }

    /**
     * @return array<int|mixed|string>
     * @throws Exception
     */
    public function getStockOccupancyByArticleNr(mixed $articleNr): array
    {
        return $this->stockOccupancyDataHandler->getStockOccupancyByArticleNr($articleNr);
    }
}
