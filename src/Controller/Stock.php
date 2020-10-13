<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Repository\StockLocationRepository;
use WebWMS\Repository\StockRotationRepository;

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
}
