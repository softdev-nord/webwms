<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\StockLocation;
use WebWMS\Exception\NotFoundException;
use WebWMS\Service\DataHandlers\Stock\StockLocationDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationService
 */
class StockLocationService
{
    public function __construct(
        private StockLocationDataHandler $stockLocationDataHandler
    ) {
    }

    /**
     * @throws Exception
     */
    public function getAllStockLocations(): JsonResponse
    {
        return $this->stockLocationDataHandler->getAllStockLocation();
    }

    public function getStockLocationByCoordinate(int $stockLocationCoordinate): ?StockLocation
    {
        return $this->stockLocationDataHandler
            ->getStockLocationByCoordinate(
                $stockLocationCoordinate
            );
    }

    /**
     * @throws NotFoundException
     * @return object[]
     */
    public function getSockLocationDetailsById(string $stockLocationId): array
    {
        return $this->stockLocationDataHandler->getSockLocationDetailsById($stockLocationId);
    }

    public function addStockLocation(Request $request): void
    {
        $this->stockLocationDataHandler->addStockLocation($request);
    }

    public function updateStockLocation(Request $request): ?StockLocation
    {
        return $this->stockLocationDataHandler->updateStockLocation($request);
    }

    public function deleteStockLocation(StockLocation $stockLocation): void
    {
        $this->stockLocationDataHandler->deletestockLocation($stockLocation);
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllStockLocationsForSelect(): array
    {
        return $this->stockLocationDataHandler->getAllStockLocationsForSelect();
    }

    /**
     * @throws NotFoundException
     * @return object[]
     */
    public function getAllStockLocationsAjax(): array
    {
        return $this->stockLocationDataHandler->getAllStockLocationsAjax();
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllFreeStockLocations(string $stockSystem): array
    {
        return $this->stockLocationDataHandler->getAllFreeStockLocations($stockSystem);
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllFreeStockLocationsWithLimit(string $stockSystem, int $limit): array
    {
        return $this->stockLocationDataHandler->getAllFreeStockLocationsWithLimit($stockSystem, $limit);
    }

    /**
     * @throws Exception
     * @return object[]
     */
    public function getFirstFreeStockLocation(string $stockSystem, int $limit): array
    {
        $freeStockLocation = [];
        $stockLocations = $this->getAllFreeStockLocationsWithLimit($stockSystem, $limit);

        foreach ($stockLocations as $stockLocation) {
            if ($stockLocation['belegt'] !== true) {
                $freeStockLocation[] = $stockLocation;
            }
        }

        return $freeStockLocation;
    }

    /**
     * @param  array<string> $stockLocation
     * @return array<int, array<string, int|string>>
     */
    public function getRemainder(array $stockLocation, int|null $remainder): array
    {
        $freeStockLocations = [];

        if (isset($remainder)) {
            $freeStockLocations[] = [
                'ln' => $stockLocation['ln'],
                'fb' => $stockLocation['fb'],
                'sp' => $stockLocation['sp'],
                'tf' => $stockLocation['tf'],
                'lnKomplett' => $stockLocation['ln'] . '-' . $stockLocation['fb'] . '-' . $stockLocation['sp'] . '-' . $stockLocation['tf'],
                'koordinate' => $stockLocation['koordinate'],
                'system' => $stockLocation['system'],
                'quantity' => $remainder,
            ];
        }

        return $freeStockLocations;
    }
}
