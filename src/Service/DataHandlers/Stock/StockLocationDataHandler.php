<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\StockLocation;
use WebWMS\Exception\NotFoundException;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationDataHandler
 */
class StockLocationDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
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
            ->findOneBy(['stockLocationCoordinate' => $stockLocationCoordinate]);
    }

    public function getSockLocationDetailsByCoordinate($coordinate): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stock_location_coordinate' => $coordinate]);

        if (!$stockLocation) {
            throw new NotFoundException('Keine Details für den gewählten Lagerort gefunden.');
        }

        return (array) $stockLocation;
    }

    public function getSockLocationDetailsById($stockLocationId): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findBy(['stockLocationId' => $stockLocationId]);

        if (!$stockLocation) {
            throw new NotFoundException('Keine Details für den gewählten Lagerort gefunden.');
        }

        return $stockLocation;
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
        $stockLocation->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->update($stockLocation);

        return $stockLocation;
    }

    /**
     * @throws Exception
     */
    public function getAllStockLocationsQuery($stockSystem): array
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('
            sl.stock_location_id AS id,
            sl.stock_location_coordinate AS koordinate,
            sl.stock_location_ln AS ln,
            sl.stock_location_fb AS fb,
            sl.stock_location_sp AS sp,
            sl.stock_location_tf AS tf,
            sl.stock_location_desc,
            (SELECT SUM((SELECT IF(tr_type = 1, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) - (SELECT SUM((SELECT IF(tr_type = 2, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) AS lp_bestand')
            ->from('transport_history', 'tph')
            ->rightJoin('tph', 'stock_location', 'sl', 'tph.stock_coordinate = sl.stock_location_coordinate')
            ->where('sl.stock_location_desc = :system')
            ->setParameter('system', $stockSystem)
            ->groupBy('sl.stock_location_coordinate');

        $stmt = $queryBuilder->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getAllStockLocationsForSelect(): array
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('stock_location_ln, stock_location_desc')
            ->from('stock_location')
            ->groupBy('stock_location_ln');

        $stmt = $queryBuilder->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    public function getAllFreeStockLocations($stockSystem): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            if (null !== $result['lp_bestand']) {
                continue;
            }
            $allResults[] = [
                'id' => $result['id'],
                'ln' => $result['ln'],
                'fb' => $result['fb'],
                'sp' => $result['sp'],
                'tf' => $result['tf'],
                'lnKomplett' => $result['ln'].'-'.$result['fb'].'-'.$result['sp'].'-'.$result['tf'],
                'koordinate' => $result['koordinate'],
                'system' => $result['stock_location_desc'],
                'belegt' => false,
            ];
        }

        return $allResults;
    }

    public function getAllFreeStockLocationsWithLimit($stockSystem, $limit): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            if (null !== $result['lp_bestand']) {
                continue;
            }
            $allResults[] = [
                'id' => $result['id'],
                'ln' => $result['ln'],
                'fb' => $result['fb'],
                'sp' => $result['sp'],
                'tf' => $result['tf'],
                'lnKomplett' => $result['ln'].'-'.$result['fb'].'-'.$result['sp'].'-'.$result['tf'],
                'koordinate' => $result['koordinate'],
                'system' => $result['stock_location_desc'],
                'belegt' => false,
            ];
        }

        return array_slice($allResults, 0, $limit);
    }

    public function getOccupiedFreeStockLocations($stockSystem, $limit): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            if (null === $result['lp_bestand']) {
                continue;
            }
            $allResults[] = [
                'ln' => $result['ln'],
                'fb' => $result['fb'],
                'sp' => $result['sp'],
                'tf' => $result['tf'],
                'lnKomplett' => $result['ln'].'-'.$result['fb'].'-'.$result['sp'].'-'.$result['tf'],
                'koordinate' => $result['koordinate'],
                'system' => $result['stock_location_desc'],
                'belegt' => true,
            ];
        }

        return array_slice($allResults, 0, $limit);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function generateStockLocation(Request $request): void
    {
        $stockLocations = $this->generateStockLocationValues($request);

        if (isset($request->request->all()['stock_location']['stock_location_check'])) {
            foreach ($stockLocations as $stockLocation) {
                $setStockLocations = new StockLocation();
                $setStockLocations->setStockLocationLn((int) $stockLocation['stock_location_ln']);
                $setStockLocations->setStockLocationFb((int) $stockLocation['stock_location_fb']);
                $setStockLocations->setStockLocationSp((int) $stockLocation['stock_location_sp']);
                $setStockLocations->setStockLocationTf((int) $stockLocation['stock_location_tf']);
                $setStockLocations->setStockLocationCoordinate((int) $stockLocation['stock_location_coordinate']);
                $setStockLocations->setStockLocationDesc($stockLocation['stock_location_desc']);
                $setStockLocations->setStockLocationWidth($stockLocation['stock_location_width']);
                $setStockLocations->setStockLocationDepth($stockLocation['stock_location_depth']);
                $setStockLocations->setStockLocationHeight($stockLocation['stock_location_height']);
                $setStockLocations->setStockLocationZone($stockLocations['stock_location_zone']);
                $setStockLocations->setCreatedAt($this->dateTimeService->createDateTime());

                $this->entityManager->persist($setStockLocations);
                $this->entityManager->flush();
            }
        } else {
            $setStockLocations = new StockLocation();
            $setStockLocations->setStockLocationLn((int) $stockLocations['stock_location_ln']);
            $setStockLocations->setStockLocationFb((int) $stockLocations['stock_location_fb']);
            $setStockLocations->setStockLocationSp((int) $stockLocations['stock_location_sp']);
            $setStockLocations->setStockLocationTf((int) $stockLocations['stock_location_tf']);
            $setStockLocations->setStockLocationCoordinate((int) $stockLocations['stock_location_coordinate']);
            $setStockLocations->setStockLocationDesc($stockLocations['stock_location_desc']);
            $setStockLocations->setStockLocationWidth($stockLocations['stock_location_width']);
            $setStockLocations->setStockLocationDepth($stockLocations['stock_location_depth']);
            $setStockLocations->setStockLocationHeight($stockLocations['stock_location_height']);
            $setStockLocations->setStockLocationZone($stockLocations['stock_location_zone']);
            $setStockLocations->setCreatedAt($this->dateTimeService->createDateTime());

            $this->entityManager->persist($setStockLocations);
            $this->entityManager->flush();
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function generateStockLocationValues($request): array
    {
        $stockLocations = [];
        $stockLocation = $request->request->all()['stock_location'];

        if (!isset($request->request->all()['stock_location']['stock_location_check'])) {
            $stockLocations['stock_location_ln'] = $stockLocation['stock_location_ln'];
            $stockLocations['stock_location_fb'] = (string) $stockLocation['stock_location_fb'];
            $stockLocations['stock_location_sp'] = (string) $stockLocation['stock_location_sp'];
            $stockLocations['stock_location_tf'] = (string) $stockLocation['stock_location_tf'];
            $stockLocations['stock_location_coordinate'] =
                $stockLocation['stock_location_ln']
                .$this->generateStockCoordinateLevel((string) $stockLocation['stock_location_fb'])
                .$this->generateStockCoordinateLevel((string) $stockLocation['stock_location_sp'])
                .$this->generateStockCoordinateLevel((string) $stockLocation['stock_location_tf'])
            ;
            $stockLocations['stock_location_desc'] = $stockLocation['stock_location_desc'];
            $stockLocations['stock_location_width'] = $stockLocation['stock_location_width'];
            $stockLocations['stock_location_depth'] = $stockLocation['stock_location_depth'];
            $stockLocations['stock_location_height'] = $stockLocation['stock_location_height'];
        } else {
            for ($fbn = 1; $fbn <= $stockLocation['stock_location_fb']; ++$fbn) {
                for ($spn = 1; $spn <= $stockLocation['stock_location_sp']; ++$spn) {
                    for ($tfn = 1; $tfn <= $stockLocation['stock_location_tf']; ++$tfn) {
                        $generatedStockLocation['stock_location_ln'] = $stockLocation['stock_location_ln'];
                        $generatedStockLocation['stock_location_fb'] = (string) $fbn;
                        $generatedStockLocation['stock_location_sp'] = (string) $spn;
                        $generatedStockLocation['stock_location_tf'] = (string) $tfn;
                        $generatedStockLocation['stock_location_coordinate'] =
                            $stockLocation['stock_location_ln']
                            .$this->generateStockCoordinateLevel((string) $fbn)
                            .$this->generateStockCoordinateLevel((string) $spn)
                            .$this->generateStockCoordinateLevel((string) $tfn)
                        ;
                        $generatedStockLocation['stock_location_desc'] = $stockLocation['stock_location_desc'];
                        $generatedStockLocation['stock_location_width'] = $stockLocation['stock_location_width'];
                        $generatedStockLocation['stock_location_depth'] = $stockLocation['stock_location_depth'];
                        $generatedStockLocation['stock_location_height'] = $stockLocation['stock_location_height'];
                        $stockLocations[] = $generatedStockLocation;
                    }
                }
            }
        }

        return $stockLocations;
    }

    public function generateStockCoordinateLevel($string): string
    {
        return str_pad($string, 4, '0', STR_PAD_LEFT);
    }
}
