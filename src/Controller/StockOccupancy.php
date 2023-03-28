<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Stock\StockOccupancyService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancy
 */
class StockOccupancy extends AbstractController
{
    public function __construct(
        private StockOccupancyService $stockOccupancyService,
        private StockLocationService $stockLocationService,
        private RequirementsService $requirementsService
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
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerbelegungen',
                'stockOccupancy' => $this->getAllStockOccupancy(),
            ]
        );
    }

    #[Route('/grafische_lagerbelegung', name: 'stock_occupancy_graphical')]
    public function stockOccupancyGraphical(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->getStockOccupancyResults($request);
    }

    /**
     * @throws Exception
     * @return array<int, array<string, mixed>>
     */
    #[Route('/stock_occupancy_ajax', name: 'stock_occupancy_ajax')]
    public function getAllStockOccupancy(): array
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
    public function getStockOccupancyResults(Request $request): Response
    {
        if ($request->attributes->get('stock_location_ln') !== null) {
            $stockLocationLn = $request->attributes->get('stock_location_ln');
        } else {
            $stockLocationLn = $this->stockLocationService->getAllStockLocationsForSelect()[0]['stock_location_ln'];
        }

        $allStockOccupancy = $this->stockOccupancyService->getAllStockOccupancyByLn(intval($stockLocationLn));
        $stockResults = [];
        $stock = [];

        foreach ($allStockOccupancy as $stockOccupancy) {
            match ($stockOccupancy['system']) {
                'Block-Lager' => $stockResults[$stockOccupancy['sp']][] = $stockOccupancy,
                default => $stockResults[$stockOccupancy['fb']][] = $stockOccupancy,
            };
            $stock[] = $stockOccupancy['system'];
        }

        return $this->render(
            'stock/stock_occupancy_graphical.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerbelegungen',
                'stockSelect' => $this->stockLocationService->getAllStockLocationsForSelect(),
                'stockResults' => array_reverse($stockResults, true),
                'stockSystem' => $stock,
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
                intval($request->attributes->get('article_nr'))
            );

        return $this->render(
            'modal/show_article_stock_details_modal.html.twig',
            [
                'articleStockDetails' => $article, true,
            ]
        );
    }
}
