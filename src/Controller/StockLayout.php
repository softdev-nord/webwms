<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Service\Stock\StockLayoutService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLayout
 */
class StockLayout extends AbstractController
{
    /** @var StockLayoutService */
    private $stockLayoutService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        StockLayoutService $stockLayoutService,
        Requirements $requirements
    ) {
        $this->stockLayoutService = $stockLayoutService;
        $this->requirements = $requirements;
    }

    /**
     * @Route("/lagerlayout", name="stock_layout")
     */
    public function stockLayout(): Response
    {
        return $this->render('stock/stock_layout.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerlayout',
                'stocklayout' => $this->stockLayoutService->getStockLayout(),
            ]
        );
    }
}