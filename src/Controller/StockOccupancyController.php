<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Stock\StockOccupancyService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyController
 */
class StockOccupancyController extends AbstractController
{
    public function __construct(
        private readonly StockOccupancyService $stockOccupancyService,
        private readonly StockLocationService $stockLocationService,
        private readonly RequirementsService $requirementsService
    ) {
    }

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
            ]
        );
    }

    #[Route('/grafische_lagerbelegung', name: 'stock_occupancy_graphical')]
    public function stockOccupancyGraphical(Request $request): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
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
    public function getStockOccupancyResults(Request $request): Response
    {
        if ($request->attributes->getInt('stock_location_ln') !== 0) {
            $stockLocationLn = $request->attributes->getInt('stock_location_ln');
        } else {
            $selectedStockLocations = $this->stockLocationService->getAllStockLocationsForSelect();
            $stockLocationLn = $selectedStockLocations[0]['stockLocationLn'];
        }

        $allStockOccupancy = $this->stockOccupancyService->getAllStockOccupancyByLn($stockLocationLn);
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
                $request->attributes->getInt('article_nr')
            );

        return $this->render(
            'modal/show_article_stock_details_modal.html.twig',
            [
                'articleStockDetails' => $article, true,
            ]
        );
    }
}
