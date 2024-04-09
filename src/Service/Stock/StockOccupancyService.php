<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyService
 */
class StockOccupancyService
{
    public function __construct(
        private readonly StockOccupancyDataHandler $stockOccupancyDataHandler
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
     *@throws Exception|JsonException
     * @return array<int, array<string, mixed>>
     */
    public function getStockOccupancyByCoordinate(Request $request): array
    {
        $stockLocationCoordinate = $request->attributes->get('stock_location_coordinate');
        $stockOccupancyDetail = [];
        $stockOccupancies = json_decode((string) $this->getAllStockOccupancy()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($stockOccupancies as $stockOccupancy) {
            if ($stockOccupancy['koordinate'] === $stockLocationCoordinate) {
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
}
