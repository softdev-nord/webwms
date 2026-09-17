<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Dto\StockLocationValuesDto;
use WebWMS\Entity\StockLocation;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLocationDataHandler'
)]
readonly class StockLocationDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
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

    public function getStockLocationById(int $stockLocationId): ?StockLocation
    {
        return $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stockLocationId' => $stockLocationId]);
    }

    /**
     * @throws Exception|\Exception
     * @return object[]
     */
    public function getStockLocationDetailsById(string $stockLocationId): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findBy(['stockLocationId' => $stockLocationId]);

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

        $result = $queryBuilder->executeQuery();

        $results = $result->fetchAllAssociative();

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
            so.in_stock,
            so.incoming_stock,
            so.reserved_stock')
            ->from('stock_location', 'sl')
            ->join('sl', 'stock_occupancy', 'so', 'sl.stock_location_coordinate = so.stock_coordinate')
            ->where('sl.stock_location_desc = :system')
            ->setParameter('system', $stockSystem)
            ->groupBy('sl.stock_location_coordinate');

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<object>
     */
    public function getAllStockLocationsForSelect(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('sl.stockLocationLn, sl.stockLocationDesc')
            ->from(StockLocation::class, 'sl')
            ->groupBy('sl.stockLocationLn');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws Exception
     * @return object[]
     */
    public function getAllStockLocationsAjax(): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findAll();

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
    public function getAllFreeStockLocationsWithLimit(string $stockSystem, int $limit, float $leQuantity): array
    {
        $allResults = [];
        $results = $this->getAllStockLocationsQuery($stockSystem);

        foreach ($results as $result) {
            $sumInventory = $result['in_stock'] + $result['incoming_stock'] + $result['reserved_stock'];
            if ($sumInventory === $leQuantity) {
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
     * @return array<int, array<string, mixed>>
     */
    public function getOccupiedStockLocationsByArticleId(int $articleId): array
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
            sl.stock_location_desc AS stockLocationDesc,
            so.in_stock AS inStock,
            so.incoming_stock AS incomingStock,
            so.reserved_stock AS reservedStock')
            ->from('stock_occupancy', 'so')
            ->rightJoin('so', 'stock_location', 'sl', 'so.stock_location_id = sl.stock_location_id')
            ->where('so.article_id = :article_id')
            ->setParameter('article_id', $articleId);

        return $queryBuilder->executeQuery()->fetchAllAssociative();
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
     * @SuppressWarnings(ElseExpression)
     */
    public function generateStockLocationValues(Request $request): array
    {
        $stockLocations = [];
        /** @var array<string|float> $stockLocation */
        $stockLocation = $request->request->all()['add_stock_location'];

        $stockLocationValuesDto = StockLocationValuesDto::hydrate($stockLocation);

        if (!isset($stockLocation['stock_location_check'])) {
            $stockLocations['stockLocationLn'] = $stockLocationValuesDto->getStockLocationLn();
            $stockLocations['stockLocationFb'] = $stockLocationValuesDto->getStockLocationFb();
            $stockLocations['stockLocationSp'] = $stockLocationValuesDto->getStockLocationSp();
            $stockLocations['stockLocationTf'] = $stockLocationValuesDto->getStockLocationTf();
            $stockLocations['stockLocationCoordinate']
                = $stockLocationValuesDto->getStockLocationLn()
                . $this->generateStockCoordinateLevel($stockLocationValuesDto->getStockLocationFb())
                . $this->generateStockCoordinateLevel($stockLocationValuesDto->getStockLocationSp())
                . $this->generateStockCoordinateLevel($stockLocationValuesDto->getStockLocationTf())
            ;
            $stockLocations['stockLocationDesc'] = $stockLocationValuesDto->getStockLocationDesc();
            $stockLocations['stockLocationWidth'] = $stockLocationValuesDto->getStockLocationWidth();
            $stockLocations['stockLocationDepth'] = $stockLocationValuesDto->getStockLocationDepth();
            $stockLocations['stockLocationHeight'] = $stockLocationValuesDto->getStockLocationHeight();
            $stockLocations['stockLocationZone'] = $stockLocationValuesDto->getStockLocationZone();
        } else {
            for ($fbn = 1; $fbn <= $stockLocation['stockLocationFb']; ++$fbn) {
                for ($spn = 1; $spn <= $stockLocation['stockLocationSp']; ++$spn) {
                    for ($tfn = 1; $tfn <= $stockLocation['stockLocationTf']; ++$tfn) {
                        $generatedStockLocation = [];

                        $generatedStockLocation['stockLocationLn'] = (string) $stockLocation['stockLocationLn'];
                        $generatedStockLocation['stockLocationFb'] = (string) $fbn;
                        $generatedStockLocation['stockLocationSp'] = (string) $spn;
                        $generatedStockLocation['stockLocationTf'] = (string) $tfn;
                        $generatedStockLocation['stockLocationCoordinate']
                            = $stockLocation['stockLocationLn']
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
