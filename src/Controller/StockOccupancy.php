<?php

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @throws Exception
     */
    #[Route('/lagerbelegung', name: 'stock_occupancy')]
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

    #[Route('/grafische_lagerbelegung', name: 'stock_occupancy_graphical')]
    public function stockOccupancyGraphical(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->getStockOccupancyResults($request);
    }

    /**
     * @throws Exception
     */
    #[Route('/stock_occupancy_ajax', name: 'stock_occupancy_ajax')]
    public function getAllStockOccupancy(): JsonResponse
    {
        return $this->stockOccupancyService->getAllStockOccupancy();
    }

    /**
     * @throws Exception
     */
    #[Route('/stock_occupancy_ajax/{stock_location_coordinate}', name: 'stock_occupancy_ajax_coordinate')]
    public function getStockOccupancyByCoordinate(Request $request): Response
    {
        $stockResults = $this->stockOccupancyService->getStockOccupancyByCoordinate($request);

        return $this->render(
            'modal/show_stock_details_modal.html.twig',
            [
                'stockDetails' => $stockResults[0], true,
            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/stock_occupancy_ajax/stock_location_ln/{stock_location_ln}', name: 'stock_occupancy_ajax_ln')]
    public function getStockOccupancyByLn(Request $request): Response
    {
        return $this->getStockOccupancyResults($request);
    }

    /**
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
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

        foreach ($allStockOccupancy as $stock) {
            match ($stock['system']) {
                'Block-Lager' => $stockResults[$stock['sp']][] = $stock,
                default => $stockResults[$stock['fb']][] = $stock,
            };
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

    /**
     * @throws Exception
     */
    #[Route('/stock_occupancy_ajax_article/{article_nr}', name: 'stock_occupancy_ajax_article')]
    public function getStockOccupancyByArticle(Request $request): Response
    {
        $article = $this->stockOccupancyService
            ->getStockOccupancyByArticleNr(
                $request->attributes->get('article_nr')
            );

        return $this->render(
            'modal/show_article_stock_details_modal.html.twig',
            [
                'articleStockDetails' => $article, true,
            ]
        );
    }
}
