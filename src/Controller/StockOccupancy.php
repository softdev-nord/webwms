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
    public function __construct(
        private StockOccupancyService $stockOccupancyService,
        private StockLocationService $stockLocationService,
        private Requirements $requirements
    ) {
    }

    /**
     * @Route("/lagerbelegung", name="stock_occupancy")
     * @throws Exception
     */
    public function index(): Response
    {
        return $this->render(
            'stock/stock_occupancy.html.twig',
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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->getStockOccupancyResults($request);
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
     * @Route("/stock_occupancy_ajax/{stock_location_coordinate}", name="stock_occupancy_ajax")
     * @throws Exception
     */
    public function getStockOccupancyByCoordinate(Request $request): JsonResponse
    {
        return $this->stockOccupancyService->getStockOccupancyByCoordinate($request);
    }

    /**
     * @Route("/stock_occupancy_ajax/stock_location_ln/{stock_location_ln}", name="stock_occupancy_ajax_ln")
     * @throws Exception
     */
    public function getStockOccupancyByLn(Request $request): Response
    {
        return $this->getStockOccupancyResults($request);
    }

    /**
     * @throws Exception
     */
    public function getStockOccupancyResults($request): Response
    {
        if ($request->attributes->get('stock_location_ln')) {
            $stockLocationLn = $request->attributes->get('stock_location_ln');
        } else {
            $stockLocationLn = $this->stockLocationService->getAllStockLocationsForSelect()[0]['stock_location_ln'];
        }

        $allStockOccupancy = $this->stockOccupancyService->getAllStockOccupancyByLn($stockLocationLn);
        $stockResults = [];
        $stockSystem = [];

        foreach ($allStockOccupancy as $stock) {
            if ($stock['system'] === 'Block-Lager') {
                $stockResults[$stock['sp']][] = $stock;
            } else {
                $stockResults[$stock['fb']][] = $stock;
            }
        }

        return $this->render(
            'stock/stock_occupancy_graphical.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerbelegungen',
                'stockSelect' => $this->stockLocationService->getAllStockLocationsForSelect(),
                'stockResults' => array_reverse($stockResults, true),
                'stockSystem' => $stock['system'],
            ]
        );
    }
}
