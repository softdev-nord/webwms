<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockOccupancyService'
)]
readonly class StockOccupancyService
{
    public function __construct(
        private StockOccupancyDataHandler $stockOccupancyDataHandler,
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
     * @throws Exception
     * @return array<int, array<string, mixed>>
     */
    public function getStockOccupancyByCoordinate(Request $request): array
    {
        /** @var string $stockLocationCoordinate */
        $stockLocationCoordinate = $request->attributes->get('stock_location_coordinate');
        $stockOccupancyDetail = [];
        $stockOccupancies = json_decode((string) $this->getAllStockOccupancy()->getContent(), true);

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
}
