<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLocation;
use WebWMS\Repository\StockLocationRepository;

/**
 * @package:    WebWMS\Service\DataHandlers
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationDataHandler
 */
class StockLocationDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockLocationRepository $stockLocationRepository
    ) {
    }

    public function save(StockLocation $stockLocation): void
    {
        $this->entityManager->persist($stockLocation);
        $this->entityManager->flush();
    }

    public function update(StockLocation $stockLocation): void
    {
        $this->entityManager->persist($stockLocation);
        $this->entityManager->flush();
    }

    public function delete(StockLocation $stockLocation): void
    {
        $this->entityManager->remove($stockLocation);
        $this->entityManager->flush();
    }

    /**
     * @return StockLocation|null Returns an array of StockLocation objects
     */
    public function getStockLocationById(int $stockLocationId): ?StockLocation
    {
        return $this->entityManager
            ->getRepository(StockLocation::class)
            ->find($stockLocationId);
    }

    /**
     * @throws Exception
     */
    public function getAllStockLocation(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('stock_location');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function updateStockLocation($requestData): ?StockLocation
    {
        $stockLocation = $this->stockLocationRepository
            ->findOneBy(['stock_location_coordinate' => $requestData['stock_location_coordinate']]);

        if (!$stockLocation) {
            return null;
        }

        $stockLocation->setStockLocationLn($requestData['stock_location_ln']);
        $stockLocation->setStockLocationFb($requestData['stock_location_fb']);
        $stockLocation->setStockLocationSp($requestData['stock_location_sp']);
        $stockLocation->setStockLocationTf($requestData['stock_location_tf']);
        $stockLocation->setStockLocationCoordinate($requestData['stock_location_coordinate']);
        $stockLocation->setStockLocationDesc($requestData['stock_location_desc']);
        $stockLocation->setStockLocationWidth($requestData['stock_location_width']);
        $stockLocation->setStockLocationDepth($requestData['stock_location_depth']);
        $stockLocation->setStockLocationHeight($requestData['stock_location_height']);

        $this->update($stockLocation);

        return $stockLocation;
    }
}
