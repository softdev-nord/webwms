<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Exception\NotFoundException;
use WebWMS\Form\StockLocationType;
use WebWMS\Service\Stock\StockLocationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocation
 */
class StockLocation extends AbstractController
{
    /** @var StockLocationService */
    private $stockLocationService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        StockLocationService $stockLocationService,
        Requirements $requirements
    ) {
        $this->stockLocationService = $stockLocationService;
        $this->requirements = $requirements;
    }

    /**
     * @throws NotFoundException
     */
    public function getAllStockLocations(): array
    {
        return $this->stockLocationService->getAllStockLocations();
    }

    /**
     * @Route("/lagerplatz_anlegen", name="add_stock_location")
     */
    public function addNewStockLocation(Request $request)
    {
        $form = $this->createForm(StockLocationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->stockLocationService->generateStockLocation($request);
            $this->addFlash('success', 'Die Lagerplätze wurden erfolgreich angelegt.');

            return $this->redirectToRoute('add_stock_location');
        }

        return $this->render('stock/add_new_stock_location.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplatz anlegen',
                'stockLocationForm' => $form->createView(),
            ]
        );
    }

    /**
     * @throws NotFoundException
     */
    public function getFreeStockLocation(): array
    {
        return $this->stockLocationService->getFreeStockLocations();
    }

    /**
     * @Route("/lagerplatz", name="stock_location")
     * @throws NotFoundException
     */
    public function stockLocations(): Response
    {
        return $this->render('stock/stock_location.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplätze',
                'stockLocation' => $this->getAllStockLocations(),
            ]
        );
    }

    /**
     * @Route("/lagerplatz_details/{stock_location_coordinate}", name="show_stock_location_details", methods={"GET","POST"})
     * @throws NotFoundException
     */
    public function getSockLocationDetailsById($coordinate): array
    {
        return $this->stockLocationService->getSockLocationDetailsById($coordinate);
    }
}
