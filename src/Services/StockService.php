<?php

declare(strict_types=1);

namespace WebWMS\Services;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\StockLocation;
use WebWMS\Entity\StockRotation;

/**
 * @package:    WebWMS\Services
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockService
 */
class StockService
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
    * @return StockLocation[]
    */
    public function getAllStockLocations(): array
    {
        $stockLocation = $this->doctrine
            ->getRepository(StockLocation::class)->findAll();

        if (!$stockLocation) {
            throw $this->createNotFoundException('Keine Lagerorte gefunden');
        }

        return $stockLocation;
    }

    public function getAllStockRotations(): JsonResponse
    {
        return $this->doctrine
            ->getRepository(StockRotation::class)
            ->getAllStockRotationsWithJoin();
    }

    public function getAllStockOccupancy(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

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

        $stmt = $queryBuilder->execute();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    /**
     * Get all Customer Orders for Ajax-Request.
     *
     * @return JsonResponse
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function getAllStockRotationsWithJoin(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select('str.id, bm.bm_short, bm.bm_desc,
                    CONCAT(stl.stock_location_ln, "-", stl.stock_location_fb, "-", stl.stock_location_sp, "-", stl.stock_location_tf) AS stock_location,
                    stl.stock_location_desc, art.article_nr, art.article_name, str.pos_quantity, usr.username, str.access_date, str.dispatch_date')
            ->from('stock_rotation', 'str')
            ->innerJoin('str', 'stock_location', 'stl', 'stl.id = str.stock_location_id')
            ->innerJoin('str', 'article', 'art', 'art.id = str.article_id')
            ->innerJoin('str', 'user', 'usr', 'usr.id = str.usr_id')
            ->innerJoin('str', 'booking_method', 'bm', 'bm.id = str.movement_id')
            ->groupBy('str.id');

        $stmt = $queryBuilder->execute();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    public function setStockRotation(array $stockRotation)
    {
        $em = $this->doctrine->getManager();

        $setStockRotation = new StockLocation();
        $setStockRotation->setStockLocationLn($stockRotation['ln']);
        $setStockRotation->setStockLocationFb($stockRotation['fb']);
        $setStockRotation->setStockLocationSp($stockRotation['sp']);
        $setStockRotation->setStockLocationTf($stockRotation['tf']);
        $setStockRotation->setStockLocationDesc($stockRotation['desc']);
        $setStockRotation->setStockLocationWidth($stockRotation['width']);
        $setStockRotation->setStockLocationDepth($stockRotation['depth']);
        $setStockRotation->setStockLocationHeight($stockRotation['height']);

        $em->persist($setStockRotation);
        $em->flush();
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}