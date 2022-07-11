<?php

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Stock\StockOccupancyService;

class StockOccupancy extends AbstractController
{
    /** @var StockOccupancyService */
    private $stockOccupancyService;

    /** @var StockLocationService */
    private $stockLocationService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        StockOccupancyService $stockOccupancyService,
        StockLocationService $stockLocationService,
        Requirements $requirements
    ) {
        $this->stockOccupancyService = $stockOccupancyService;
        $this->stockLocationService = $stockLocationService;
        $this->requirements = $requirements;
    }

    /**
     * @Route("/stock_occupancy_ajax", name="stock_occupancy_ajax")
     * @throws Exception
     */
    public function getAllStockOccupancy(): JsonResponse
    {
        return $this->stockOccupancyService->getAllStockOccupancy();
    }

    /**
     * @Route("/lagerbelegung", name="stock_occupancy")
     * @throws Exception
     */
    public function stockOccupancy(): Response
    {
        return $this->render('stock/stock_occupancy.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerbelegungen',
                'stockOccupancy' => $this->getAllStockOccupancy(),
            ]
        );
    }

    /**
     * @Route("/grafische_lagerbelegung", name="stock_occupancy_graphical")
     * @throws Exception
     */
    public function stockOccupancyGraphical(Request $request): Response
    {
        $stockLocationCoordinate = '141';

        $result = $this->stockOccupancyService->getAllStockOccupancyByLn($stockLocationCoordinate);
        $stockResults = [];

        foreach ( $result as $stock ) {
            if ($stock['system'] === 'Block-Lager') {
                $stockResults[$stock['sp']][] = $stock;
            } else {
                $stockResults[$stock['fb']][] = $stock;
            }
        }

        return $this->render('stock/stock_occupancy_graphical.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerbelegungen',
                'stockSelect' => $this->stockLocationService->getAllStockLocationsForSelect(),
                'stockResults' => array_reverse($stockResults, true),
            ]
        );
    }

}