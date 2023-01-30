<?php

declare(strict_types=1);

namespace WebWMS\Bundles\StockInventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements;
use WebWMS\Repository\StockLocationRepository;

class DefaultController extends AbstractController
{
    /** @var StockLocationRepository */
    private $stockLocationRepository;

    public function __construct(
        StockLocationRepository $stockLocationRepository
    ) {
        $this->stockLocationRepository = $stockLocationRepository;
    }

    public function getAllStockLocations(): array
    {
        return $this->stockLocationRepository->findAll();
    }

    /**
     * @Route("/inventur_starten", name="stock_inventory_start")
     */
    public function index(): Response
    {
        return $this->render('@StockInventory/stock_inventory/stock_inventory_start.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Inventur starten',
            'stockLocation' => $this->getAllStockLocations(),
        ]);
    }
}
