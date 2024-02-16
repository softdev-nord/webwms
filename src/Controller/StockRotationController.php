<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockRotationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationController
 */
class StockRotationController extends AbstractController
{
    public function __construct(
        private readonly StockRotationService $stockRotationService,
        private readonly RequirementsService $requirementsService
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/lagerbewegung', name: 'stock_rotation')]
    public function stockRotations(): Response
    {
        return $this->render(
            'stock/stock_rotation.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerbewegungen',
                'stockRotation' => $this->getAllStockRotations(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/stock_rotation_ajax', name: 'stock_rotation_ajax')]
    public function getAllStockRotations(): JsonResponse
    {
        return $this->stockRotationService->getAllStockRotationsWithJoin();
    }
}
