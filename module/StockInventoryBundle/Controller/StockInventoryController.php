<?php

declare(strict_types=1);

namespace WebWMS\Bundles\StockInventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Entity\StockLocation;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\StockLocationRepository;
use WebWMS\Service\RequirementsService;

#[ClassInformation(
    package: 'WebWMS\Bundles\StockInventoryBundle\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInventoryController'
)]
class StockInventoryController extends AbstractController
{
    public function __construct(
        private readonly StockLocationRepository $stockLocationRepository,
        private readonly RequirementsService $requirementsService,
    ) {
    }

    /**
     * @return array<StockLocation>
     */
    public function getAllStockLocations(): array
    {
        return $this->stockLocationRepository->findAll();
    }

    #[Route('/inventur_starten', name: 'stock_inventory_start')]
    public function index(): Response
    {
        return $this->render(
            '@StockInventory/stock_inventory/stock_inventory_start.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Inventur starten',
                'stockLocation' => $this->getAllStockLocations(),
            ]
        );
    }
}
