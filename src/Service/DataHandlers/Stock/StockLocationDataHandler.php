<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationDataHandler
 */
class StockLocationDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
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

    public function getStockLocationByCoordinate(int $stockLocationCoordinate): ?StockLocation
    {
        return $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stock_location_coordinate' => $stockLocationCoordinate]);
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
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stock_location_coordinate' => $requestData['stock_location_coordinate']]);

        if (!$stockLocation) {
            return null;
        }

        $stockLocation->setStockLocationLn((int) $requestData['stock_location_ln']);
        $stockLocation->setStockLocationFb((int) $requestData['stock_location_fb']);
        $stockLocation->setStockLocationSp((int) $requestData['stock_location_sp']);
        $stockLocation->setStockLocationTf((int) $requestData['stock_location_tf']);
        $stockLocation->setStockLocationCoordinate((string) $requestData['stock_location_coordinate']);
        $stockLocation->setStockLocationDesc((string) $requestData['stock_location_desc']);
        $stockLocation->setStockLocationWidth((string) $requestData['stock_location_width']);
        $stockLocation->setStockLocationDepth((string) $requestData['stock_location_depth']);
        $stockLocation->setStockLocationHeight((string) $requestData['stock_location_height']);
        $stockLocation->setStockLocationZone((string) $requestData['stock_location_zone']);
        $stockLocation->setStockLocationUpdatedAt($updatedAt);

        $this->update($stockLocation);

        return $stockLocation;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getAllStockLocations($stockSystem, $limit): array
    {
        $allResults = [];
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('
            sl.stock_location_coordinate AS koordinate,
            sl.stock_location_ln AS ln,
            sl.stock_location_fb AS fb,
            sl.stock_location_sp AS sp,
            sl.stock_location_tf AS tf,
            sl.stock_location_desc,
            (SELECT SUM((SELECT IF(tr_typ = 1, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) - (SELECT SUM((SELECT IF(tr_typ = 2, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) AS lp_bestand')
            ->from('transport_history', 'tph')
            ->rightJoin('tph', 'stock_location', 'sl', 'tph.stock_coordinate = sl.stock_location_coordinate')
            ->where('sl.stock_location_desc = :system')
            ->setParameter('system', $stockSystem)
            ->groupBy('sl.stock_location_coordinate')->setMaxResults($limit);

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        foreach ($results as $result) {
            if ($result['lp_bestand'] > 0) {
                $allResults[] = [
                    'ln' => $result['ln'],
                    'fb' => $result['fb'],
                    'sp' => $result['sp'],
                    'tf' => $result['tf'],
                    'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                    'koordinate' => $result['koordinate'],
                    'system' => $result['stock_location_desc'],
                    'belegt' => true
                ];
            } else {
                $allResults[] = [
                    'ln' => $result['ln'],
                    'fb' => $result['fb'],
                    'sp' => $result['sp'],
                    'tf' => $result['tf'],
                    'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                    'koordinate' => $result['koordinate'],
                    'system' => $result['stock_location_desc'],
                    'belegt' => false
                ];
            }
        }

        return $allResults;
    }
}
