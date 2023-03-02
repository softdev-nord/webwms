<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLayoutService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLayout
 */
class StockLayout extends AbstractController
{
    public function __construct(
        private StockLayoutService $stockLayoutService,
        private RequirementsService $requirementsService
    ) {
    }

    #[Route('/lagerlayout', name: 'stock_layout')]
    public function index(): Response
    {
        return $this->render(
            'stock/stock_layout.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerlayout',
                'stocklayout' => $this->stockLayoutService->getStockLayout(),
            ]
        );
    }
}
