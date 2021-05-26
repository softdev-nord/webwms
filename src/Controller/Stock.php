<?php

namespace WebWMS\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Controller\Requirements as Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Repository\StockLocationRepository;
use WebWMS\Repository\StockRotationRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class Stock extends AbstractController
{
    /**
     * @var StockLocationRepository
     */
    private $stockLocationRepository;

    /**
     * @var StockRotationRepository
     */
    private $stockRotationRepository;



    public function __construct(
        StockLocationRepository $stockLocationRepository,
        StockRotationRepository $stockRotationRepository
    )
    {
        $this->stockLocationRepository = $stockLocationRepository;
        $this->stockRotationRepository = $stockRotationRepository;
    }

    /**
     * @return \WebWMS\Entity\StockLocation[]
     */
    public function getAllStockLocations()
    {
        $stockLocation = $this->stockLocationRepository->findAll();

        if (!$stockLocation) {
            throw $this->createNotFoundException(
                'Keine Lagerorte gefunden'
            );
        }

        return $stockLocation;
    }

    /**
     * @Route("/stock_rotation_ajax", name="stock_rotation")
     */
    public function getAllStockRotations()
    {
        $stockRotations = $this->stockRotationRepository->getAllStockRotationsWithJoin();

        if (!$stockRotations) {
            throw $this->createNotFoundException(
                'Keine Lagerbewegungen gefunden'
            );
        }

        return $stockRotations;
    }

    /**
     * @Route("/stock_occupancy_ajax", name="stock_occupancy_ajax")
     */
    public function getAllStockOccupancy()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = "SELECT
                tph.id AS id,
                tph.stock_coordinate AS koordinate,
                tph.stock_nr AS ln,
                tph.stock_level1 AS fb,
                tph.stock_level2 AS sp,
                tph.stock_level3 AS tf,
                tph.su_id AS lagereinheit,
                tph.art_nr AS art_nr,
                art.art_name AS bezeichnung,
                if(ta.tr_typ = 1, (SELECT SUM(tr_quantity) FROM transport_request WHERE tr_typ = 1 AND stock_coordinate = if(tph.stock_coordinate = ta.stock_coordinate, ta.stock_coordinate, tph.stock_coordinate)), 0.000) AS trans_ein,
                if(ta.tr_typ = 2, (SELECT SUM(tr_quantity) FROM (SELECT (SUM(IF(transport_history.tr_typ = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_typ = '2', transport_history.tr_quantity, 0.000))) FROM transport_history GROUP BY transport_history.stock_coordinate) AS lp_bestand,transport_request WHERE tr_typ = 2 AND stock_coordinate = if(tph.stock_coordinate = ta.stock_coordinate, ta.stock_coordinate, tph.stock_coordinate)), 0.000) AS trans_aus,
-- (SELECT if(tph.koordinate = ta.koordinate, (SELECT SUM(menge) FROM transportauftrag WHERE ta_typ = '2' AND ta.koordinate = tph.koordinate ), 0.000)) AS trans_aus,
--    SUM(if(tph.tr_typ = '1' AND tph.stock_coordinate = ta.stock_coordinate, tph.tr_quantity, 0.000) - if(tph.tr_typ = '2' AND tph.stock_coordinate = ta.stock_coordinate, tph.tr_quantity, 0.000)) AS lp_bestand, -- Ergebnis aktueller Bestand x Einträge in Tabelle transportauftrag ?
--                 (SELECT SUM((SELECT IF(tph.tr_typ = '1', tph.tr_quantity, 0.000)) - (SELECT IF(tph.tr_typ = '2', tph.tr_quantity, 0.000))) FROM transport_history GROUP BY stock_coordinate LIMIT 1) AS lp_bestand,
--                (SELECT SUM(IF(tph.tr_typ = '1', tph.tr_quantity, 0.000)) - SUM(IF(tph.tr_typ = '2', tph.tr_quantity, 0.000)) FROM transport_history GROUP BY tph.stock_coordinate ORDER BY tph.stock_coordinate) AS lp_bestand,
                /*(SELECT ( 
                        SELECT SUM(IF(tr_typ = '1', tr_quantity, 0.000)) FROM transport_history GROUP BY tph.stock_coordinate LIMIT 1
                        )
                        -
                        (
                        SELECT SUM(IF(tr_typ = '2', tr_quantity, 0.000)) FROM transport_history GROUP BY tph.stock_coordinate LIMIT 1
                        )
                ) AS lp_bestand,*/
                (SELECT (SUM(IF(transport_history.tr_typ = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_typ = '2', transport_history.tr_quantity, 0.000))) FROM transport_history GROUP BY transport_history.stock_coordinate LIMIT 1) AS lp_bestand,
-- (SELECT SUM(if(tph.tr_typ = '1', tph.tr_quantity, 0.000) - if(tph.tr_typ = '2', tph.tr_quantity, 0.000)) FROM transport_history WHERE stock_coordinate = if(tph.koordinate = ta.koordinate, tph.koordinate, ta.koordinate)) AS lp_bestand,
                (SELECT tph.tr_access FROM transport_history WHERE tph.tr_typ = '1' AND tph.stock_coordinate = if(tph.stock_coordinate = ta.stock_coordinate, ta.stock_coordinate, tph.stock_coordinate) ORDER BY id DESC LIMIT 1) AS letzter_zugang,
                (SELECT tph.tr_dispatch FROM transport_history WHERE tph.tr_typ = '2' AND tph.stock_coordinate = if(tph.stock_coordinate = ta.stock_coordinate, ta.stock_coordinate, tph.stock_coordinate) ORDER BY id DESC LIMIT 1) AS letzter_abgang
                FROM transport_history AS tph
                    LEFT JOIN transport_request AS ta
                        ON tph.stock_coordinate = ta.stock_coordinate
                    INNER JOIN article AS art
                        ON tph.art_nr = art.art_nr
                GROUP BY tph.stock_coordinate
                ORDER BY tph.stock_coordinate";

        $data = $conn->fetchAll($sql);


        return new JsonResponse($data);
    }



    /**
     * @Route("/lagerplatz", name="stock_location")
     */
    public function stockLocations()
    {
        return $this->render('stock/stock_location.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Lagerplätze',
            'stockLocation' => $this->getAllStockLocations(),
        ]);
    }

    /**
     * @Route("/lagerbewegung", name="stock_rotation")
     */
    public function stockRotations()
    {
        return $this->render('stock/stock_rotation.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Lagerbewegungen',
            'stockRotation' => $this->getAllStockRotations(),
        ]);
    }

    /**
     * @Route("/lagerbelegung", name="stock_occupancy")
     */
    public function stockOccupancy()
    {
        return $this->render('stock/stock_occupancy.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Lagerbelegungen',
            'stockOccupancy' => $this->getAllStockOccupancy(),
        ]);
    }
}
