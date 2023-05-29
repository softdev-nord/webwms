<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\StockLocation;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
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

    public function delete(StockLocation $stockLocation): void
    {
        $this->entityManager->remove($stockLocation);
        $this->entityManager->flush();
    }

    public function getStockLocationByCoordinate(string $stockLocationCoordinate): ?StockLocation
    {
        return $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stockLocationCoordinate' => $stockLocationCoordinate]);
    }

    /**
     * @throws \Exception
     * @return object[]
     */
    public function getStockLocationDetailsById(string $stockLocationId): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findBy(['stockLocationId' => $stockLocationId]);

        if ($stockLocation == null) {
            throw new \Exception('Keine Details für den gewählten Lagerort gefunden.');
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

    public function addStockLocation(StockLocation $stockLocation): void
    {
        $stockLocation->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLocation);
    }

    public function updateStockLocation(StockLocation $stockLocation): void
    {
        $stockLocation->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLocation);
    }

    public function deleteStockLocation(StockLocation $stockLocation): void
    {
        $this->delete($stockLocation);
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllStockLocationsQuery(string $stockSystem): array
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
     * @return array<string|int|mixed>
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

    /**
     * @throws \Exception
     * @return object[]
     */
    public function getAllStockLocationsAjax(): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findAll();

        if ($stockLocation == null) {
            throw new \Exception('Keine Lagerorte gefunden');
        }

        return $stockLocation;
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllFreeStockLocations(string $stockSystem): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            if ($result['lp_bestand'] !== null) {
                continue;
            }
            $allResults[] = [
                'id' => $result['id'],
                'ln' => $result['ln'],
                'fb' => $result['fb'],
                'sp' => $result['sp'],
                'tf' => $result['tf'],
                'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                'koordinate' => $result['koordinate'],
                'system' => $result['stock_location_desc'],
                'belegt' => false,
            ];
        }

        return $allResults;
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllFreeStockLocationsWithLimit(string $stockSystem, int $limit): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            if ($result['lp_bestand'] !== null) {
                continue;
            }
            $allResults[] = [
                'id' => $result['id'],
                'ln' => $result['ln'],
                'fb' => $result['fb'],
                'sp' => $result['sp'],
                'tf' => $result['tf'],
                'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                'koordinate' => $result['koordinate'],
                'system' => $result['stock_location_desc'],
                'belegt' => false,
            ];
        }

        return array_slice($allResults, 0, $limit);
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getOccupiedFreeStockLocations(string $stockSystem, int $limit): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            if ($result['lp_bestand'] === null) {
                continue;
            }
            $allResults[] = [
                'ln' => $result['ln'],
                'fb' => $result['fb'],
                'sp' => $result['sp'],
                'tf' => $result['tf'],
                'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                'koordinate' => $result['koordinate'],
                'system' => $result['stock_location_desc'],
                'belegt' => true,
            ];
        }

        return array_slice($allResults, 0, $limit);
    }

    /**
     * @return array<int|string, array<string, float|string>|float|string>
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function generateStockLocationValues(Request $request): array
    {
        $stockLocations = [];
        $stockLocation = $request->request->all()['add_stock_location'];

        if (!isset($stockLocation['stock_location_check'])) {
            $stockLocations['stockLocationLn'] = strval($stockLocation['stockLocationLn']);
            $stockLocations['stockLocationFb'] = strval($stockLocation['stockLocationFb']);
            $stockLocations['stockLocationSp'] = strval($stockLocation['stockLocationSp']);
            $stockLocations['stockLocationTf'] = strval($stockLocation['stockLocationTf']);
            $stockLocations['stockLocationCoordinate'] =
                $stockLocation['stockLocationLn']
                . $this->generateStockCoordinateLevel(strval($stockLocation['stockLocationFb']))
                . $this->generateStockCoordinateLevel(strval($stockLocation['stockLocationSp']))
                . $this->generateStockCoordinateLevel(strval($stockLocation['stockLocationTf']))
            ;
            $stockLocations['stockLocationDesc'] = strval($stockLocation['stockLocationDesc']);
            $stockLocations['stockLocationWidth'] = floatval($stockLocation['stockLocationWidth']);
            $stockLocations['stockLocationDepth'] = floatval($stockLocation['stockLocationDepth']);
            $stockLocations['stockLocationHeight'] = floatval($stockLocation['stockLocationHeight']);
            $stockLocations['stockLocationZone'] = strval($stockLocation['stockLocationZone']);
        } else {
            for ($fbn = 1; $fbn <= $stockLocation['stockLocationFb']; ++$fbn) {
                for ($spn = 1; $spn <= $stockLocation['stockLocationSp']; ++$spn) {
                    for ($tfn = 1; $tfn <= $stockLocation['stockLocationTf']; ++$tfn) {
                        $generatedStockLocation = [];

                        $generatedStockLocation['stockLocationLn'] = (string) $stockLocation['stockLocationLn'];
                        $generatedStockLocation['stockLocationFb'] = (string) $fbn;
                        $generatedStockLocation['stockLocationSp'] = (string) $spn;
                        $generatedStockLocation['stockLocationTf'] = (string) $tfn;
                        $generatedStockLocation['stockLocationCoordinate'] =
                            $stockLocation['stockLocationLn']
                            . $this->generateStockCoordinateLevel((string) $fbn)
                            . $this->generateStockCoordinateLevel((string) $spn)
                            . $this->generateStockCoordinateLevel((string) $tfn)
                        ;
                        $generatedStockLocation['stockLocationDesc'] = (string) $stockLocation['stockLocationDesc'];
                        $generatedStockLocation['stockLocationWidth'] = (float) $stockLocation['stockLocationWidth'];
                        $generatedStockLocation['stockLocationDepth'] = (float) $stockLocation['stockLocationDepth'];
                        $generatedStockLocation['stockLocationHeight'] = (float) $stockLocation['stockLocationHeight'];
                        $generatedStockLocation['stockLocationZone'] = (string) $stockLocation['stockLocationZone'];
                        $stockLocations[] = $generatedStockLocation;
                    }
                }
            }
        }

        return $stockLocations;
    }

    public function generateStockCoordinateLevel(string $string): string
    {
        return str_pad($string, 4, '0', STR_PAD_LEFT);
    }
}
