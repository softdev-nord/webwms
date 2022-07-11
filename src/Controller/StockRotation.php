<?php

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Service\Stock\StockLayoutService;
use WebWMS\Service\Stock\StockRotationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockRotation
 */
class StockRotation extends AbstractController
{
    /** @var StockRotationService */
    private $stockRotationService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        StockRotationService $stockRotationService,
        Requirements $requirements
    ) {
        $this->stockRotationService = $stockRotationService;
        $this->requirements = $requirements;
    }

    /**
     * @Route("/stock_rotation_ajax", name="stock_rotation_ajax")
     *
     * @throws Exception
     */
    public function getAllStockRotations(): JsonResponse
    {
        return $this->stockRotationService->getAllStockRotationsWithJoin();
    }

    /**
     * @Route("/lagerbewegung", name="stock_rotation")
     *
     * @throws Exception
     */
    public function stockRotations(): Response
    {
        return $this->render('stock/stock_rotation.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerbewegungen',
                'stockRotation' => $this->getAllStockRotations(),
            ]
        );
    }
}