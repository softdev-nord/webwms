<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockOccupancyService
 */
class StockOccupancyService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws Exception
     */
    public function getAllStockOccupancy(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('tph.id AS id,
            tph.stock_coordinate AS koordinate,
            tph.stock_nr AS ln,
            tph.stock_level1 AS fb,
            tph.stock_level2 AS sp,
            tph.stock_level3 AS tf,
            tph.su_id AS lagereinheit,
            tph.article_nr AS art_nr,
            art.article_name AS bezeichnung,
            (SELECT SUM((SELECT IF(stock_coordinate = ta.stock_coordinate AND tr_typ = 1, tr_quantity, 0.000))) FROM transport_request GROUP BY stock_coordinate LIMIT 1) AS trans_ein,
            (SELECT SUM((SELECT IF(stock_coordinate = ta.stock_coordinate AND tr_typ = 2, tr_quantity, 0.000))) FROM transport_request GROUP BY stock_coordinate LIMIT 1) AS trans_aus,
            (SELECT SUM((SELECT IF(tr_typ = 1, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) - (SELECT SUM((SELECT IF(tr_typ = 2, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) AS lp_bestand,
            (SELECT IF(tr_typ = 1, DATE_FORMAT(tr_access,"%d.%m.%Y %T"), "") FROM transport_history WHERE stock_coordinate = tph.stock_coordinate ORDER BY tr_access DESC LIMIT 1) AS letzter_zugang,
            (SELECT IF(tr_typ = 2, DATE_FORMAT(tr_dispatch,"%d.%m.%Y %T"), "") FROM transport_history WHERE stock_coordinate = tph.stock_coordinate ORDER BY tr_dispatch DESC LIMIT 1) AS letzter_abgang')
            ->from('transport_history', 'tph')
            ->leftJoin('tph', 'transport_request', 'ta', 'tph.stock_coordinate = ta.stock_coordinate')
            ->innerJoin('tph', 'article', 'art', 'tph.article_nr = art.article_nr')
            ->groupBy('tph.stock_coordinate');

        $stmt = $queryBuilder->executeQuery();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    /**
     * @throws Exception
     */
    public function getStockOccupancyByCoordinate(Request $request): JsonResponse
    {
        $stockLocationCoordinate = $request->attributes->get('stock_location_coordinate');
        $stockOccupancyDetail = [];
        $getAllStockOccupancy = $this->getAllStockOccupancy();
        $stockOccupancies = json_decode($getAllStockOccupancy->getContent(), true);

        foreach ($stockOccupancies as $stockOccupancy) {
            if ($stockOccupancy['koordinate'] === $stockLocationCoordinate) {
                $stockOccupancyDetail[] = $stockOccupancy;
            }
        }

        return new JsonResponse($stockOccupancyDetail);
    }

    /**
     * @throws Exception
     */
    public function getAllStockOccupancyByLn($stockLocationLn): array
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
            ->andWhere('sl.stock_location_ln = :stock_location_ln')
            ->setParameter('stock_location_ln', $stockLocationLn)
            ->groupBy('sl.stock_location_coordinate');

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
