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

    public function delete(StockLocation $stockLocation): void
    {
        $this->entityManager->remove($stockLocation);
        $this->entityManager->flush();
    }

    public function getStockLocationByCoordinate(int $stockLocationCoordinate): ?StockLocation
    {
        return $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stockLocationCoordinate' => $stockLocationCoordinate]);
    }

    /**
     * @throws NotFoundException
     * @return object[]
     */
    public function getStockLocationDetailsById(string $stockLocationId): array
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

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function addStockLocation(Request $request): void
    {
        $stockLocations = $this->generateStockLocationValues($request);
        $setStockLocations = new StockLocation();

        if (isset($request->request->all()['add_stock_location']['stock_location_check'])) {
            foreach ($stockLocations as $stockLocation) {
                $setStockLocations->setStockLocationLn($stockLocation['stockLocationLn']);
                $setStockLocations->setStockLocationFb($stockLocation['stockLocationFb']);
                $setStockLocations->setStockLocationSp($stockLocation['stockLocationSp']);
                $setStockLocations->setStockLocationTf($stockLocation['stockLocationTf']);
                $setStockLocations->setStockLocationCoordinate($stockLocation['stockLocationCoordinate']);
                $setStockLocations->setStockLocationDesc($stockLocation['stockLocationDesc']);
                $setStockLocations->setStockLocationWidth($stockLocation['stockLocationWidth']);
                $setStockLocations->setStockLocationDepth($stockLocation['stockLocationDepth']);
                $setStockLocations->setStockLocationHeight($stockLocation['stockLocationHeight']);
                $setStockLocations->setStockLocationZone($stockLocations['stockLocationZone']);
                $setStockLocations->setCreatedAt($this->dateTimeService->createDateTime());

                $this->entityManager->persist($setStockLocations);
                $this->entityManager->flush();
            }
        } else {
            $setStockLocations->setStockLocationLn($stockLocations['stockLocationLn']);
            $setStockLocations->setStockLocationFb($stockLocations['stockLocationFb']);
            $setStockLocations->setStockLocationSp($stockLocations['stockLocationSp']);
            $setStockLocations->setStockLocationTf($stockLocations['stockLocationTf']);
            $setStockLocations->setStockLocationCoordinate($stockLocations['stockLocationCoordinate']);
            $setStockLocations->setStockLocationDesc($stockLocations['stockLocationDesc']);
            $setStockLocations->setStockLocationWidth($stockLocations['stockLocationWidth']);
            $setStockLocations->setStockLocationDepth($stockLocations['stockLocationDepth']);
            $setStockLocations->setStockLocationHeight($stockLocations['stockLocationHeight']);
            $setStockLocations->setStockLocationZone($stockLocations['stockLocationZone']);
            $setStockLocations->setCreatedAt($this->dateTimeService->createDateTime());

            $this->entityManager->persist($setStockLocations);
            $this->entityManager->flush();
        }
    }

    public function updateStockLocation(Request $request): ?StockLocation
    {
        $requestData = $request->request->all()['edit_stock_location'];
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stockLocationCoordinate' => $requestData['stockLocationCoordinate']]);

        if (!$stockLocation) {
            return null;
        }

        $stockLocation->setStockLocationLn((int) $requestData['stockLocationLn']);
        $stockLocation->setStockLocationFb((int) $requestData['stockLocationFb']);
        $stockLocation->setStockLocationSp((int) $requestData['stockLocationSp']);
        $stockLocation->setStockLocationTf((int) $requestData['stockLocationTf']);
        $stockLocation->setStockLocationCoordinate((string) $requestData['stockLocationCoordinate']);
        $stockLocation->setStockLocationDesc((string) $requestData['stockLocationDesc']);
        $stockLocation->setStockLocationWidth((float) $requestData['stockLocationWidth']);
        $stockLocation->setStockLocationDepth((float) $requestData['stockLocationDepth']);
        $stockLocation->setStockLocationHeight((float) $requestData['stockLocationHeight']);
        $stockLocation->setStockLocationZone((string) $requestData['stockLocationZone']);
        $stockLocation->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLocation);

        return $stockLocation;
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
     * @throws NotFoundException
     * @return object[]
     */
    public function getAllStockLocationsAjax(): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findAll();

        if (!$stockLocation) {
            throw new NotFoundException('Keine Lagerorte gefunden');
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
     * @return array<string|int|mixed>
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function generateStockLocationValues(Request $request): array
    {
        $stockLocations = [];
        $stockLocation = $request->request->all()['add_stock_location'];

        if (!isset($stockLocation['stock_location_check'])) {
            $stockLocations['stockLocationLn'] = $stockLocation['stockLocationLn'];
            $stockLocations['stockLocationFb'] = (string) $stockLocation['stockLocationFb'];
            $stockLocations['stockLocationSp'] = (string) $stockLocation['stockLocationSp'];
            $stockLocations['stockLocationTf'] = (string) $stockLocation['stockLocationTf'];
            $stockLocations['stockLocationCoordinate'] =
                $stockLocation['stockLocationLn']
                . $this->generateStockCoordinateLevel((string) $stockLocation['stockLocationFb'])
                . $this->generateStockCoordinateLevel((string) $stockLocation['stockLocationSp'])
                . $this->generateStockCoordinateLevel((string) $stockLocation['stockLocationTf'])
            ;
            $stockLocations['stockLocationDesc'] = $stockLocation['stockLocationDesc'];
            $stockLocations['stockLocationWidth'] = $stockLocation['stockLocationWidth'];
            $stockLocations['stockLocationDepth'] = $stockLocation['stockLocationDepth'];
            $stockLocations['stockLocationHeight'] = $stockLocation['stockLocationHeight'];
        } else {
            for ($fbn = 1; $fbn <= $stockLocation['stockLocationFb']; ++$fbn) {
                for ($spn = 1; $spn <= $stockLocation['stockLocationSp']; ++$spn) {
                    for ($tfn = 1; $tfn <= $stockLocation['stockLocationTf']; ++$tfn) {
                        $generatedStockLocation['stockLocationLn'] = $stockLocation['stockLocationLn'];
                        $generatedStockLocation['stockLocationFb'] = (string) $fbn;
                        $generatedStockLocation['stockLocationSp'] = (string) $spn;
                        $generatedStockLocation['stockLocationTf'] = (string) $tfn;
                        $generatedStockLocation['stockLocationCoordinate'] =
                            $stockLocation['stockLocationLn']
                            . $this->generateStockCoordinateLevel((string) $fbn)
                            . $this->generateStockCoordinateLevel((string) $spn)
                            . $this->generateStockCoordinateLevel((string) $tfn)
                        ;
                        $generatedStockLocation['stockLocationDesc'] = $stockLocation['stockLocationDesc'];
                        $generatedStockLocation['stockLocationWidth'] = $stockLocation['stockLocationWidth'];
                        $generatedStockLocation['stockLocationDepth'] = $stockLocation['stockLocationDepth'];
                        $generatedStockLocation['stockLocationHeight'] = $stockLocation['stockLocationHeight'];
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
